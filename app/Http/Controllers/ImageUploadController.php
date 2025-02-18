<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessImageUpload;
use Illuminate\Support\Facades\Validator;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed for image upload', $validator->errors()->toArray());
            return response()->json(['error' => $validator->errors()], 422);
        }


        $path = $request->file('image')->store('uploads', 'public');

        \Log::info('Image stored at path: ' . $path);


        ProcessImageUpload::dispatch($path);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'path' => $path
        ], 200);
    }
}
