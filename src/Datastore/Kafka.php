<?php

namespace KafkaStore\LibrenmsKafkaStorePlugin\Datastore;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use RdKafka\Conf;
use RdKafka\FFI\Library;
use RdKafka\Message;
use RdKafka\Producer;
use LibreNMS\Data\Store\BaseDatastore;
use App\Polling\Measure\Measurement;

class Kafka extends BaseDatastore
{
    private $client = null;
    private $device_id = 0;
    private static $settings = [];
    private $kafkaFlushTimeout = 50;

    public function __construct(Producer $client, array $settings)
    {
        parent::__construct();
        
        // throw exception if settings is not an array or if it is empty
        if (!is_array($settings) || empty($settings)) {
            throw new \InvalidArgumentException('Settings must be a non-empty array.');
        }

        self::$settings = $settings;
        // Check if the client is an instance of Producer
        if (!($client instanceof Producer)) {
            throw new \InvalidArgumentException('Client must be an instance of RdKafka\Producer.');
        }
        
        $this->client = $client;

        // Cache the flush timeout value early to avoid Config during shutdown
        if ($this->kafkaFlushTimeout == null) {
            $this->kafkaFlushTimeout = $settings['kafka_flush_timeout'] ?? 50;
        }

        // Register shutdown function
        register_shutdown_function(function () {
            $this->safeFlush();
        });
    }

    public function __destruct()
    {
        $this->safeFlush();
        // Clear reference
        $this->client = null;
    }

    public static function getClient(array $settings): Producer
    {
        $conf = new Conf();
        // Set the log level
        $conf->set('log_level', (string) LOG_DEBUG);
        // Set the log callback for exceptions
        $conf->setDrMsgCb(
            function (Producer $producer, Message $message): void {
                if ($message->err !== RD_KAFKA_RESP_ERR_NO_ERROR) {
                    error_log($message->errstr());
                }
            }
        );
        // Set the log callback for logs
        $conf->setLogCb(
            function (Producer $producer, int $level, string $facility, string $message): void {
                error_log('KAFKA: ' . $message);
            }
        );

        // Set the kafka broker servers
        $conf->set('bootstrap.servers', $settings['kafka_brokers'] ?? 'localhost:9092');
        // Set the idempotence
        $conf->set('enable.idempotence', $settings['kafka_idempotency'] ?? false ? 'true' : 'false');
        // Max queue allowed messages in poller memory
        $conf->set('queue.buffering.max.messages', $settings['kafka_buffer_max_size'] ?? 100000);
        // Num of messages each call to kafka
        $conf->set('batch.num.messages', $settings['kafka_batch_size'] ?? 25);
        // Max wait time to acumulate before sending the batch
        $conf->set('linger.ms', $settings['kafka_linger'] ?? 500);
        // Change ACK
        $conf->set(
            'request.required.acks',
            $settings['kafka_required_acks'] ?? ($settings['kafka_idempotency'] ?? false ? 'all' : '1')
        );

        // check if debug for ssl was set and enable it
        $confKafkaSSLDebug = $settings['kafka_security_debug'] ?? null;
        $confKafkaSSLDebug != null || strlen($confKafkaSSLDebug) !== 0 ? $conf->set('debug', $confKafkaSSLDebug) : null;

        // config ssl
        $isSslEnabled = $settings['kafka_ssl_enable'] ?? false;
        if ($isSslEnabled) {
            $conf->set('security.protocol', 'ssl');
            $conf->set('ssl.endpoint.identification.algorithm', 'none');

            // prepare all necessary librenms kafka config with associated rdkafka key
            $kafkaSSLConfigs = [
                'kafka_ssl_keystore_location' => 'ssl.key.location',
                'kafka_ssl_keystore_password' => 'ssl.key.password',
            ];

            // fetch kafka config values, if exists, associate its value to rdkafka key
            foreach ($kafkaSSLConfigs as $configKey => $kafkaKey) {
                $configValue = $settings[$configKey] ?? null;
                $configValue != null || strlen($configValue) !== 0 ? $conf->set($kafkaKey, $configValue) : null;
            }
        }

        return new Producer($conf);
    }

    public function safeFlush()
    {
        // check if client instance exists
        if ($this->client === null) {
            return;
        }

        try {
            // get total number of messages in the queue
            $outQLen = $this->client->getOutQLen();

            if ($outQLen > 0) {
                // During shutdown, Log facades might not work properly, use d_echo as fallback
                error_log("KAFKA: Flushing {$outQLen} remaining messages");

                // Use cached timeout value to avoid Config during shutdown
                $result = $this->client->flush($this->kafkaFlushTimeout);

                if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
                    $error_msg = sprintf(
                        'KAFKA: Flush failed. Error: %s, Code: %d, Device ID: %d, Remaining: %d',
                        Library::rd_kafka_err2str($result),
                        $result,
                        $this->device_id,
                        $this->client->getOutQLen()
                    );

                    error_log($error_msg);
                }
            }
        } catch (\Throwable $e) {
            $error_msg = 'KAFKA: safeFlush failed with exception. Error: ' . $e->getMessage() . '. Trace: ' . $e->getTraceAsString();
            error_log($error_msg);
        }
    }

    public function getName()
    {
        return 'Kafka';
    }

    public static function isEnabled()
    {
        return $settings['kafka_enable'] ?? false;
    }

    public function getKafkaFlushTimeout()
    {
        return $this->kafkaFlushTimeout;
    }

    /**
     * Datastore-independent function which should be used for all polled metrics.
     *
     * RRD Tags:
     *   rrd_def     RrdDefinition
     *   rrd_name    array|string: the rrd filename, will be processed with rrd_name()
     *   rrd_oldname array|string: old rrd filename to rename, will be processed with rrd_name()
     *   rrd_step             int: rrd step, defaults to 300
     *
     * @param  array  $device
     * @param  string  $measurement  Name of this measurement
     * @param  array  $tags  tags for the data (or to control rrdtool)
     * @param  array|mixed  $fields  The data to update in an associative array, the order must be consistent with rrd_def,
     *                               single values are allowed and will be paired with $measurement
     */
    public function put($device, $measurement, $tags, $fields)
    {
        try {
            // check if the device is an array and has a device_id
            if (!is_array($device) || !isset($device['device_id'])) {
                throw new \InvalidArgumentException('Device must be an array with a device_id.');
            }
            // get the singleton instance of the produced
            /** @var Producer $producer */
            $producer = $this->client;
            $this->device_id = $device['device_id'];
            $topic = $producer->newTopic(Kafka::getTopicName());

            $device_data = 'DeviceCache'::get($device['device_id']);
            $excluded_groups = self::$settings['kafka_device_groups'] ?? ''; // comman separated string
            $excluded_measurement = self::$settings['kafka_device_measurements'] ?? ''; // comman separated string
            $excluded_device_fields = self::$settings['kafka_device_fields'] ?? ''; // comman separated string
            $excluded_device_fields_arr = [];

            if ($excluded_groups != null && strlen($excluded_groups) > 0) {
                // convert into array
                $excluded_groups_arr = explode(',', strtoupper($excluded_groups));

                $device_groups = $device_data->groups;
                foreach ($device_groups as $group) {
                    // The group name will always be parsed as lowercase, even when uppercase in the GUI.
                    if (in_array(strtoupper($group->name), $excluded_groups_arr)) {
                        Log::debug('KAFKA: Skipped parsing to Kafka, device is in group: ' . $group->name);

                        return;
                    }
                }
            }

            if ($excluded_measurement != null && strlen($excluded_measurement) > 0) {
                // convert into array
                $excluded_measurement_arr = explode(',', $excluded_measurement);

                if (in_array($measurement, $excluded_measurement_arr)) {
                    Log::debug('KAFKA: Skipped parsing to Kafka, measurement is in measurement-excluded: ' . $measurement);

                    return;
                }
            }

            if ($excluded_device_fields != null && strlen($excluded_device_fields) > 0) {
                // convert into array
                $excluded_device_fields_arr = explode(',', $excluded_device_fields);
            }

            // start
            $stat = Measurement::start('write');

            $tmp_fields = [];
            $tmp_tags = [];
            $tmp_tags['device_groups'] = implode('|', $device_data->groups->pluck('name')->toArray());

            foreach ($tags as $k => $v) {
                if (empty($v)) {
                    $v = '_blank_';
                }
                $tmp_tags[$k] = $v;
            }
            foreach ($fields as $k => $v) {
                if ($k == 'time') {
                    $k = 'rtime';
                }

                if (($value = $this->forceType($v)) !== null) {
                    $tmp_fields[$k] = $value;
                }
            }

            if (empty($tmp_fields)) {
                Log::warning('KAFKA: All fields empty, skipping update', [
                    'orig_fields' => $fields,
                    'device_id' => $this->device_id,
                ]);

                return;
            }

            // create and organize data
            $filteredDeviceData = array_diff_key($device, array_flip($excluded_device_fields_arr));
            // add current time to the data
            $filteredDeviceData['current_polled_time'] = Carbon::now();

            $resultArr = [
                'measurement' => $measurement,
                'device' => $filteredDeviceData,
                'fields' => $tmp_fields,
                'tags' => $tmp_tags,
            ];

            if ($settings['kafka_debug_enabled'] ?? false) {
                Log::debug('Kafka data: ', [
                    'device_id' => $this->device_id,
                    'measurement' => $measurement,
                    'fields' => $tmp_fields,
                ]);
            }

            $dataArr = json_encode($resultArr);
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, $dataArr);
            $producer->poll(self::getKafkaFlushTimeout());

            $this->recordStatistic($stat->end());
        } catch (\Throwable $e) {
            Log::error('KAFKA: Put failed with exception', [
                'device_id' => $this->device_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function forceType($data)
    {
        /*
         * It is not trivial to detect if something is a float or an integer, and
         * therefore may cause breakages on inserts.
         * Just setting every number to a float gets around this, but may introduce
         * inefficiencies.
         */

        if (is_numeric($data)) {
            return floatval($data);
        }

        return $data === 'U' ? null : $data;
    }

    public static function getTopicName()
    {
        return $settings['kafka_topic'] ?? 'librenms';
    }

    /**
     * Checks if the datastore wants rrdtags to be sent when issuing put()
     *
     * @return bool
     */
    public function wantsRrdTags()
    {
        return false;
    }
}