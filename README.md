# Windwalker SMS AjaxImage Bundle

![p-2014-06-08-4](https://cloud.githubusercontent.com/assets/1639206/3211460/1c396912-ef19-11e3-8c85-229c0a167bbe.jpg)

## Install

Download all files to `{JOOMLA}/libraries/windwalker-bundles/SMSAjaxImageBundle`.

Use container to register it.

``` php
$option = array(
	'handler' => 'com_ihealth',
	'basePath' => 'tmp/reservation',
	'uploadFolder' => ':session',
	'clearPeriod' => 2
);

$container->registerServiceProvider(new AjaxImageProvider('reservation', $option));
```

## Options

Working...

