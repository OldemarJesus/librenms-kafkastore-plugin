<?php

namespace Murrant\LibrenmsExamplePlugin;

class Facade extends \Illuminate\Support\Facades\Facade
{
    public static function test(): string
    {
        return 'this is a test';
    }
}
