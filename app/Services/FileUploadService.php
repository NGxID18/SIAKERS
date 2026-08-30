<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * Upload a file to a specific directory in the public folder.
     *
     * @param UploadedFile $file The uploaded file instance
     * @param string $directory The directory name inside public/uploads (e.g., 'kerusakan')
     * @param string $prefix Optional prefix for the filename
     * @return string The relative path to the uploaded file
     */
    public function uploadImage(UploadedFile $file, string $directory, string $prefix = 'img'): string
    {
        $uploadDir = public_path("uploads/{$directory}");
        
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadDir, $filename);

        return "/uploads/{$directory}/{$filename}";
    }
}
