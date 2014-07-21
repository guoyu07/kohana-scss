<?php defined('SYSPATH') or die('No direct script access.');

return array(
  'compress'  => true, // styles compression
  'path'  => 'media'.DIRECTORY_SEPARATOR.'scss', // path to scss files in MOPATH or APPATH
  'url' => '/css/', // relative path to a writable folder to store compiled / compressed css

  'clear_first' => false, // Clear the provided folder before writing new file

  // paths to include in scss files
  'include_paths' => array(
    APPPATH . 'media' . DIRECTORY_SEPARATOR . 'scss' . DIRECTORY_SEPARATOR
  )
);

