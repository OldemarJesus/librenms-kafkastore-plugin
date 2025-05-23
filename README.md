LibreNMS Kafka Store Plugin

Info goes here :)

Add the path to your plugin for local development (or deployment)
    cd /opt/librenms
    composer config --global repositories.kafkastore-plugin '{"type": "path", "url": "/workspaces/librenms-plugin-project/librenms-kafkastore-plugin", "symlink": true}'

For users to use your plugin, you should publish it to packagist.org

Then run:

    lnms plugin:add oldemarjesus/librenms-kafkastore-plugin @dev