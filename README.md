LibreNMS Example Plugin

Info goes here :)

Add the path to your plugin for local development (or deployment)

    composer config --global repositories.example-plugin '{"type": "path", "url": "/full/path/to/librenms-example-plugin", "symlink": true}'

For users to use your plugin, you should publish it to packagist.org

Then run:

    lnms plugin:add murrant/librenms-example-plugin @dev
# librenms-kafkastore-plugin
