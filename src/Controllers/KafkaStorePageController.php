<?php

namespace KafkaStore\LibrenmsKafkaStorePlugin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class KafkaStorePageController extends Controller
{
    public function index(): View
    {
        return view('kafkastore-plugin::page');
    }

    public function settings(): View
    {
        return view('kafkastore-plugin::settings');
    }
}
