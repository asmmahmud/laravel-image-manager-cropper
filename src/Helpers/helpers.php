<?php

if (!function_exists('imagemanager_asset')) {
    function imagemanager_asset($path, $secure = null)
    {
        return asset(config('imagemanager.assets_path').'/'.$path, $secure);
    }
}
