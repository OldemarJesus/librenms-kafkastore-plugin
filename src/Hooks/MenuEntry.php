<?php

namespace Murrant\LibrenmsExamplePlugin\Hooks;

use LibreNMS\Interfaces\Plugins\Hooks\MenuEntryHook;

class MenuEntry implements MenuEntryHook
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array{0: string, 1: array<string, string[]>}
     */
    public function handle(string $pluginName): array
    {
        $path = realpath(__DIR__.'/../../assets/');
        $images = array_map('basename', glob($path.'/*.{gif,jpg,jpeg,png,svg}', GLOB_BRACE) ?: []);

        return ["$pluginName::menu", ['images' => $images]];
    }
}
