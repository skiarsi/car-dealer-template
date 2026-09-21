<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageResizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehiclePhotoUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'photo' => ['required', 'file', 'max:10240', 'mimes:jpeg,jpg,png,webp,gif'],
            'draft' => ['required', 'uuid'],
        ]);

        $path = ImageResizer::store($request->file('photo'), 'vehicles/pending/'.$data['draft']);

        return response()->json([
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
