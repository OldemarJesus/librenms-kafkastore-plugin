# LibreNMS Kafka Store Plugin

## Development Setup
Add the path to your plugin for local development (or deployment)

```bash
cd /opt/librenms
composer config --global repositories.kafkastore-plugin '{"type": "path", "url": "/workspaces/librenms-plugin-kafka-devcontainer/librenms-kafkastore-plugin", "symlink": true}'
```

For users to use your plugin, you should publish it to packagist.org

Then run:

```bash
lnms plugin:add oldemarjesus/librenms-kafkastore-plugin @dev
```

## Production Setup

<b>You must set extra php ini</b>

```sh
# cat <<EOF > /etc/php/8.2/cli/conf.d/20-kafka.ini
zend.max_allowed_stack_size=-1
ffi.enable="true"
EOF
```

- This should enable ffi and disable stack overflow check for PHP 8.3 onwards.

<b>You must install Apache Kafka C/C++ client library</b>

- For Debian and Ubuntu, <code>$ apt install librdkafka-dev</code>
- For Fedora, RedHat and CentOS, <code>$ yum install librdkafka-devel</code>
- Or follow the github documentation for build from source, [librdkafka - the Apache Kafka C/C++ client library](https://github.com/confluentinc/librdkafka)