<?php

namespace Murrant\LibrenmsExamplePlugin\Controllers;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    public function __invoke(string $image): BinaryFileResponse
    {
        $image = basename($image); // simple safety check, not comprehensive
        $image_path = __DIR__.'/../../assets/'.$image;

        if (! file_exists($image_path)) {
            abort(404);
        }

        return response()->file($image_path);
    }
}
