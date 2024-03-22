<?php

namespace App\Presenters;

use Illuminate\Support\Facades\Storage;
use Laracasts\Presenter\Presenter;

/** @mixin \App\Models\Image */
class ImagePresenter extends Presenter
{
    public function url(): string
    {

        $disk = env('APP_ENV') === 'production' ? 's3' : 'public';

        return Storage::disk($disk)->url($this->file_path);
    }
}