<?php

use Illuminate\Support\Facades\Route;
use KafkaStore\LibrenmsKafkaStorePlugin\Controllers\KafkaStoreController;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::post('plugin/kafka-store', [KafkaStoreController::class, 'saveConfig'])->name('kafka-store.save-config');
});