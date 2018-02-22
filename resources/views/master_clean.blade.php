<!DOCTYPE html>
<html>
<head>
    <title>Tasmnaguib/Imagemanager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400|Lato:300,400,700,900'
          rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{{ imagemanager_asset('lib/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ imagemanager_asset('lib/css/toastr.min.css') }}">
    @yield('css')
</head>
<body>
<div id="imagemanager-loader" style="display: none">
    <img src="{{ config('imagemanager.assets_path') . '/images/loading-spinner-54.gif' }}" alt="image manager Loader">
</div>
<div class="app-container">
    <div class="row content-container">
        <div class="container-fluid">
            <div class="side-body padding-top">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script src="{{ imagemanager_asset('lib/js/jquery.min.js') }}"></script>
<script src="{{ imagemanager_asset('lib/js/bootstrap.min.js') }}"></script>
<script src="{{ imagemanager_asset('lib/js/toastr.min.js') }}"></script>
@yield('javascript')
</body>
</html>