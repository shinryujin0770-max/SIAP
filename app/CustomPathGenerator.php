<?php

namespace App;

use App\Models\Situ;
use App\Models\User;
use Carbon\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        if ($media->model_type == User::class) {
            return 'users/' . $media->model_id . '/';
        }

        if ($media->model_type == Situ::class) {
            return 'situ/' . $media->model_id . '/';
        }
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive/';
    }
}
