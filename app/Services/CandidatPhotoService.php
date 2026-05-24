<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class CandidatPhotoService
{
    public function store(UploadedFile $file, ?string $oldPhoto = null, ?string $oldThumb = null): array
    {
        $this->deletePhotos($oldPhoto, $oldThumb);

        $baseName = uniqid('candidat_', true);
        $photoPath = 'candidats/'.$baseName.'.jpg';
        $thumbPath = 'candidats/'.$baseName.'_thumb.jpg';

        try {
            $image = Image::read($file->getRealPath());
            $image->cover(400, 400)->toJpeg(85)->save(Storage::disk('public')->path($photoPath));

            $thumb = Image::read($file->getRealPath());
            $thumb->cover(100, 100)->toJpeg(80)->save(Storage::disk('public')->path($thumbPath));
        } catch (\Throwable) {
            $photoPath = $file->store('candidats', 'public');
            $thumbPath = $photoPath;
        }

        return ['photo' => $photoPath, 'photo_thumb' => $thumbPath];
    }

    public function deletePhotos(?string $photo, ?string $thumb): void
    {
        if ($photo) {
            Storage::disk('public')->delete($photo);
        }

        if ($thumb && $thumb !== $photo) {
            Storage::disk('public')->delete($thumb);
        }
    }
}
