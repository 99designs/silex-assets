Silex Assets
============

Silex Assets aims to be the most minimal possible asset pipeline for using sass/requirejs in a Silex project. 

Rather than depend on Gulp/Grunt, a Makefile is provided, or a set of Makefile includes to include in your 
project.

How does it work?
-----------------

All asset compilation is done via the command-line. This means your app is ultra-lean, even in development. All assets are based around a `dist` directory, which is located in your web tree. A Makefile is used to generate a
`dist/.manifest` file which contains what files have been generated and checksums for their contents. 

The twig extension included then allows twig templates to reference files in dist, along with the checksum from
the manifest as a cache-buster.

This looks like `<link rel="stylesheet" src="/web/dist/mystylesheet.css?12312334234" />` where the number is a 
CRC32 checksum built in the `make manifest` target. 

During development you can run `make watch` and changing sass will be re-compiled, a new manifest will be built 
and if you have the LiveReload plugin your browser will update. 

Installing
----------

```bash
composer require 99designs/silex-assets
```
Then add the provider to your app.php file:

```php
<?php

$app->register(new \SilexAssets\Provider\AssetsServiceProvider(array(
    'web_path' => '/dist',
    'output_dir' => __DIR__.'/../web/dist',
    'requirejs_compiled' => $app['assets.require_compiled'],
    'requirejs_output_dir' => __DIR__.'/../web/dist/js',
    'requirejs_web_path' => '/dist/js',
)));
```

Then there are twig extensions added that use a very similar syntax to the Assetic extensions:

```html
{% stylesheets 'css/swiftly.css' 'css/asimovicons.css' %}
    <link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
```

```html
{% javascripts "js/example.js" %}
    <script src="{{ asset_url }}"></script>
{% endjavascripts %}
```

```html
{% requirejs 'bundles/frontpage' %}
```  





