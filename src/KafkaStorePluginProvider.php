<?php

namespace KafkaStore\LibrenmsKafkaStorePlugin;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use KafkaStore\LibrenmsKafkaStorePlugin\Datastore\Kafka;
use KafkaStore\LibrenmsKafkaStorePlugin\Datastore\KafkaDatastoreDecorator;
use KafkaStore\LibrenmsKafkaStorePlugin\Hooks\Settings;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook;
use LibreNMS\Interfaces\Plugins\PluginManagerInterface;

class KafkaStorePluginProvider extends ServiceProvider
{
    static public $pluginName = 'kafkastore-plugin';
    /**
     * Bootstrap any package services.
     */
    public function boot(PluginManagerInterface $pluginManager): void
    {
        $pluginName = self::$pluginName;

        // register hooks with LibreNMS (if any are desired)
        // if no hooks are defined, LibreNMS may delete the plugin from the ui
        // if you don't want any specific hooks, you can just register a settings hook
        $pluginManager->publishHook($pluginName, SettingsHook::class, Settings::class);
        $pluginManager->setSettings($pluginName, ['settings' => config('kafkastore-plugin')]);

        if (! $pluginManager->pluginEnabled($pluginName)) {
            return; // if plugin is disabled, don't boot
        }

        AboutCommand::add('Kafka Store Plugin', fn (): array => ['Version' => '1.0.0']);

        $this->loadViewsFrom(__DIR__.'/../resources/views', $pluginName);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            // __DIR__.'/../public' => public_path('vendor/kafka-store-plugin'), // files that can be published publicly
            __DIR__.'/../config/config.php' => config_path('kafkastore-plugin.php'),
        ]);

        // Decorate the original Datastore with Kafka functionality
        $this->app->extend('Datastore', function ($originalDatastore, $app) {
            return new KafkaDatastoreDecorator($originalDatastore);
        });

        // Register kafka client as a singleton
        $this->app->singleton('KafkaClient', function ($app) {
            $settings = config('kafkastore-plugin');
            return Kafka::getClient($settings);
        });
    }
}
