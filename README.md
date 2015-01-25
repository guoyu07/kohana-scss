kohana-scss
===========

Kohana 3.3 SCSS Module to compile Scss files to css with [scssphp](http://leafo.net/scssphp/) compiler

## How to use

1. Put the scss module folder in your Modules directory
2. Include scss module in your application's bootstrap: 'scss' => MODPATH.'scss'
3. Copy the scss config file from /modules/scss/config/scss.php to your application's config directory
4. Configure scss config file
5. Install dependencies via composer

	cd modules/scss && composer.phar install

or download the [scssphp](http://leafo.net/scssphp/) and [CssMin](https://github.com/natxet/CssMin) libraries into scss/vendor path.

Add this code to html head in view file:

	<head>
		<title>Kohana SCSS</title>
		<?php echo Scss::render('test'); ?>
	</head>

or you can send an array of files

	<head>
		<title>Kohana SCSS</title>
		<?php echo Scss::render(array(
			'test', 'test2'
		)); ?>
	</head>

## Description in Russian

[Kohana-scss](http://sarbas.org/posts/kohana-scss.html)
