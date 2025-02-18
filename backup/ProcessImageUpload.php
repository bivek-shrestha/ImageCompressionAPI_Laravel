<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;


    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }


    public function handle()
    {
        $storagePath = storage_path('app/public/' . $this->filePath);

        if (!file_exists($storagePath)) {
            \Log::error('File not found at path: ' . $storagePath);
            return;
        }


        $info = getimagesize($storagePath);
        if (!$info) {
            \Log::error('Failed to retrieve image info for: ' . $storagePath);
            return;
        }

        $mime = $info['mime'];
        $result = false;
        $image = null;

        try {
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($storagePath);
                    if ($image) {
                        // Save with quality set to 80%
                        $result = imagejpeg($image, $storagePath, 80);
                    }
                    break;

                case 'image/png':
                    $image = imagecreatefrompng($storagePath);
                    if ($image) {

                        $result = imagepng($image, $storagePath, 8);
                    }
                    break;

                case 'image/gif':
                    $image = imagecreatefromgif($storagePath);
                    if ($image) {

                        $result = imagegif($image, $storagePath);
                    }
                    break;

                default:
                    \Log::error('Unsupported image type: ' . $mime);
                    return;
            }

            if ($result) {
                \Log::info('Image processed using GD at: ' . $this->filePath);
            } else {
                \Log::error('Failed to process image at: ' . $this->filePath);
            }
        } catch (\Exception $e) {
            \Log::error('Exception during image processing: ' . $e->getMessage());
        } finally {
            if (is_resource($image)) {
                imagedestroy($image);
            }
        }
    }
}
