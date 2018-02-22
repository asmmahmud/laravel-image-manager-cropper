# Tasmnaguib Image Manager

## Install

Create a directory named `packages` in your project root. Download the zip version of the package and unpack it and put the directory
named `tasmnaguib_imagemanager` into this newly created directory. 

After that add the following in your main composer.json file:
``` bash
    "repositories": [
        {
            "type": "path",
            "url": "./packages/tasmnaguib_imagemanager",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "tasmnaguib/imagemanager": "dev-master"
    },
```
After that in the console run:
``` bash
    composer update
```
Publish config file and assets using the following commands:
``` bash
    php artisan vendor:publish --tag=imagemanager_config
```
``` bash
    php artisan vendor:publish --tag=imagemanager_assets
```

## Configuration
After publishing the config, you'll find a new config file named
imagemanager.php in the config directory. Here you'll find the following important 
options:
 1) `admin_url_prefix` : Url Prefix for admin panel
 2) `admin_middleware` : Admin auth middleware
 3) `quality` : default quality (0 to 100) of the uploaded image 
 4) `thumbnail_size` : Thumbnail image size in px
 5) `storage.disk` : Name of the storage engine you're using (ie, s3, local, public etc)

## Usage
After setting up the configuration properly, if you go the url: http://[domainname]/[]admin_url_prefix]/imagemanager, 
you'll see the directories and images in your storage file system. 
You can do the following things:
 1) Upload new images
 2) Crop images in various predefined ratios (ie 16:9, 3:2 etc) while uploading
 3) Create separate versions of images to display in tablet, mobile, desktop and also for thumbnail size while uploading
 4) Delete images and directories (single click for selection and crl + click for multi selection)
 5) Browse storage file system (double click the directory to enter it)
 6) Move images and directories 
 7) Rename images and directories
 
In order to properly integrate this package with your admin theme or section, you can customize package's `[package dir]/resources/views/index.blade.php` tempalte file.
This file is using a simplified master blade layout template (`[package dir]/resources/views/master_clean.blade.php`), you can use your own master layout template file.
In order customzie the `[package dir]/resources/views/index.blade.php` file, copy it to `resources/views/vendor/imagemanager` directory and then customize it.
## License

The MIT License (MIT). Please see [License File](https://github.com/dnoegel/php-xdg-base-dir/blob/master/LICENSE) for more information.
