<?php defined('SYSPATH') or die('No direct script access.');

// Turn on the minimization and building in PRODUCTION environment
$in_production = (Kohana::$environment === Kohana::PRODUCTION);

return array(
  'compress'  => $in_production, // styles compression
  'path'  => 'media'.DIRECTORY_SEPARATOR.'scss', // path to scss files in MOPATH or APPATH
  'url' => '/css/', // relative path to a writable folder to store compiled / compressed css
);