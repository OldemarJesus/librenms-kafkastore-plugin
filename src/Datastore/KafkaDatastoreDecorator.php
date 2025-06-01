<?php

namespace KafkaStore\LibrenmsKafkaStorePlugin\Datastore;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LibreNMS\Interfaces\Data\DataStorageInterface;

class KafkaDatastoreDecorator implements DataStorageInterface    
{
    protected $originalDatastore;
    protected $kafkaDatastore;

    /**
     * KafkaDatastoreDecorator constructor.
     *
     * @param $originalDatastore The original datastore to decorate
     */
    public function __construct($originalDatastore)
    {
        $this->originalDatastore = $originalDatastore;
        // Initialize Kafka datastore if enabled
        if ($this->isKafkaEnabled()) {
            try {
                $this->kafkaDatastore = new Kafka(app('KafkaClient'), config('kafkastore-plugin'));
                Log::info('KAFKA: Datastore decorator initialized successfully');
            } catch (\Throwable $e) {
                Log::error('KAFKA: Failed to initialize Kafka datastore', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->kafkaDatastore = null;
            }
        }
    }

    /**
     * Check if Kafka datastore is enabled
     *
     * @return bool
     */
    private function isKafkaEnabled(): bool
    {
        return true;
    }

    /**
     * Datastore-independent function which should be used for all polled metrics.
     *
     * This decorator first sends data to Kafka (if enabled), then delegates to the original datastore.
     *
     * @param  $device
     * @param  string  $measurement  Name of this measurement
     * @param  array  $tags  tags for the data (or to control rrdtool)
     * @param  array|mixed  $fields  The data to update in an associative array
     */
    public function put($device, $measurement, $tags, $fields)
    {
        // First delegate to the original datastore
        $this->originalDatastore->put($device, $measurement, $tags, $fields);

        // Then, send to Kafka if enabled and available
        if ($this->kafkaDatastore !== null) {
            try {
                if (! is_array($fields)) {
                    $fields = [$measurement => $fields];
                }
                $this->kafkaDatastore->put($device, $measurement, $tags, $fields);
            } catch (\Throwable $e) {
                Log::error('KAFKA: Failed to send data to Kafka', [
                    'device_id' => $device['device_id'] ?? 'unknown',
                    'measurement' => $measurement,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * Delegate all other methods to the original datastore
     */
    public function disable($name)
    {
        return $this->originalDatastore->disable($name);
    }

    public function getStores()
    {
        $stores = $this->originalDatastore->getStores();
        
        // Add Kafka to the list of stores if it's enabled
        if ($this->kafkaDatastore !== null) {
            $stores[] = $this->kafkaDatastore;
        }
        
        return $stores;
    }

    public function getStats(): Collection
    {
        $stats = $this->originalDatastore->getStats();
        
        return $stats;
    }

    /**
     * Forward static method calls to the original Datastore class
     */
    public static function init($options = [])
    {
        $originalDatastore = 'Datastore'::init($options);
        return new static($originalDatastore);
    }

    public static function terminate()
    {
        return 'Datastore'::terminate();
    }
}