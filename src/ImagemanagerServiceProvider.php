<?php

namespace Tasmnaguib\Imagemanager;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProvider;

class ImagemanagerServiceProvider extends ServiceProvider
{
    public function register() {
        $this->app->register(ImageServiceProvider::class);

        $this->loadHelpers();
        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
        }
    }
    public function boot(){
        $this->loadRoutesFrom(__DIR__.'/../routes/imagemanager.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'imagemanager');
    }
    protected function loadHelpers() {
        foreach (glob(__DIR__ . '/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    private function registerPublishableResources() {
        $publishablePath = dirname(__DIR__) . '/publishable';
        $publishable = [
            'imagemanager_assets' => [
                "{$publishablePath}/assets/" => public_path(config('imagemanager.assets_path')),
            ],
            'imagemanager_config' => [
                "{$publishablePath}/config/imagemanager.php" => config_path('imagemanager.php'),
            ],
        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    public function registerConfigs() {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/publishable/config/imagemanager.php', 'imagemanager'
        );
    }

}