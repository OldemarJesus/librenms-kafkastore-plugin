<?php

use Illuminate\Support\Facades\Route;
use KafkaStore\LibrenmsKafkaStorePlugin\Controllers\KafkaStoreController;

Route::middleware(['web', 'auth'])->group(function (): void {
});
