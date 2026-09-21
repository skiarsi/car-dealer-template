<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership;
use App\Support\ImageResizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DealershipLogoController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'icon' => ['required', 'file', 'max:5120', 'mimes:jpeg,jpg,png,webp,gif'],
        ]);

        $dealership = Dealership::current() ?? Dealership::query()->create([
            'name' => config('app.name'),
        ]);

        if ($dealership->logo) {
            Storage::disk('public')->delete($dealership->logo);
        }

        $path = ImageResizer::store($request->file('icon'), 'dealership', 256, 'png');
        $dealership->update(['logo' => $path]);

        return response()->json([
            'path' => $path,
            'url' => $dealership->fresh()->logoUrl(),
        ]);
    }
}
