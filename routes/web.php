<?php

use Illuminate\Support\Facades\Route;
use KafkaStore\LibrenmsKafkaStorePlugin\Controllers\ImageController;
use KafkaStore\LibrenmsKafkaStorePlugin\Controllers\KafkaStorePageController;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('plugin/kafkastore-page', [KafkaStorePageController::class, 'index'])->name('kafkastore-plugin.page');
    Route::get('plugin/kafkastore-page/images/{image}', ImageController::class)->name('kafkastore-plugin.image');
});
