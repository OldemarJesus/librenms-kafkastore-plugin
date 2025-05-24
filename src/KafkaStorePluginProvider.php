<?php

namespace KafkaStore\LibrenmsKafkaStorePlugin;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use KafkaStore\LibrenmsKafkaStorePlugin\Hooks\MenuEntry;
use KafkaStore\LibrenmsKafkaStorePlugin\Hooks\Settings;
use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook;
use LibreNMS\Interfaces\Plugins\Hooks\SettingsHook;
use LibreNMS\Interfaces\Plugins\PluginManagerInterface;

class KafkaStorePluginProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(PluginManagerInterface $pluginManager): void
    {
        $pluginName = 'kafkastore-plugin';

        // register hooks with LibreNMS (if any are desired)
        // if no hooks are defined, LibreNMS may delete the plugin from the ui
        // if you don't want any specific hooks, you can just register a settings hook
        $pluginManager->publishHook($pluginName, MenuEntryHook::class, MenuEntry::class);
        $pluginManager->publishHook($pluginName, SettingsHook::class, Settings::class);

        if (! $pluginManager->pluginEnabled($pluginName)) {
            return; // if plugin is disabled, don't boot
        }

        AboutCommand::add('Kafka Store Plugin', fn (): array => ['Version' => '1.0.0']);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', $pluginName);

        $this->publishes([
            // __DIR__.'/../public' => public_path('vendor/kafka-store-plugin'), // files that can be published publicly
            __DIR__.'/../config/config.php' => config_path('kafkastore-plugin.php'),
        ]);
    }
}
