<?php

namespace Encore\ChunkFileUpload;

use Encore\Admin\Admin;
use Illuminate\Support\ServiceProvider;

class ChunkFileUploadServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(ChunkFileUpload $extension)
    {
        if (!ChunkFileUpload::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'chunk-file-upload');
        }

        $this->app->booted(function () {
            ChunkFileUpload::routes(__DIR__ . '/../routes/web.php');
        });

        Admin::booting(function () {
            Admin::js('vendor/laravel-admin/js/main.js');
            Admin::js('vendor/laravel-admin/js/webuploader.js');
            Admin::css('vendor/laravel-admin/css/style.css');
            Admin::css('vendor/laravel-admin/css/webuploader.css');
        });
    }
}
