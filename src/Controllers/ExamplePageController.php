<?php

namespace Murrant\LibrenmsExamplePlugin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class ExamplePageController extends Controller
{
    public function index(): View
    {
        return view('example-plugin::page');
    }
}
