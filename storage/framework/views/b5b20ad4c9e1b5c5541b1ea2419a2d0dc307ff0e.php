<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta name="developer" content="indra08031993@gmail.com">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="sipongi-menlhk.go.id">
    <meta name="description" content="">
    <link rel="shortcut icon" href="<?php echo e(asset('images/favicon.ico')); ?>" type="image/png" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="googlebot" content="noindex, nofollow, noarchive">

    <title><?php echo e(config('app.name')); ?></title>

    
    <link href="<?php echo e(mix('/css/app.css')); ?>" rel="stylesheet">

    <script>
        window.Laravel = <?php echo json_encode([
                'siteName' => config('app.name'),
                'siteUrl' => config('app.url'),
                'locale' => config('app.locale'),
                'fallbackLocale' => config('app.fallback_locale'),
                'apiUrl' => config('app.url').
                '/api'
            ]); ?>;
    </script>
</head>

<body>
    <div id="app"></div>

    <script src="<?php echo e(mix('/js/app.js')); ?>"></script>
    <script src="<?php echo e(asset('/vendor/tinymce/tinymce.min.js')); ?>"></script>
</body>

</html><?php /**PATH /home/vagrant/sipongi-laravel/resources/views/app.blade.php ENDPATH**/ ?>