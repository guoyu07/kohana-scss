<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Kohana-scssphp
 * @author tsarbas <tsarbas@yandex.kz>
 */

class Kohana_Scss{

  // Default scss files extension
  public static $ext = 'scss';

  /**
   * Get the link tag of scss paths
   *
   * @param   mixed     array of css paths or single path
   * @param   string    value of media css type
   * @return  string    link tag pointing to the css paths
   */
  public static function render($array = '', $media = 'screen')
  {
    if (is_string($array))
    {
      $array = array($array);
    }

    // return comment if array is empty
    if (empty($array)) return self::_html_comment('no scss files');

    $stylesheets = array();
    $assets = array();

    // get config
    $config = Kohana::$config->load('scss');

    // validate
    foreach ($array as $file)
    {
      $file = Kohana::find_file($config['path'], $file, self::$ext);
      if ($file)
      {
        array_push($stylesheets, $file);
      }
      else
      {
        array_push($assets, self::_html_comment('could not find '.Debug::path($file)));
      }
    }

    // all stylesheets are invalid
    if ( ! count($stylesheets)) return self::_html_comment('all scss files are invalid');

    // if compression is allowed
    if ($config['compress'])
    {
      return HTML::style(self::_combine($stylesheets), array('media' => $media));
    }

    // if no compression
    foreach ($stylesheets as $file)
    {
      $data = file_get_contents($file);
      $data = self::_compile($data);
      $filename = self::_get_filename($file, $config['url']);
      file_put_contents(DOCROOT . $filename, $data);
      array_push($assets, html::style($filename, array('media' => $media)));
    }

    return join("\n", $assets);
  }

  /**
   * Combine the files
   *
   * @param   array    array of asset files
   * @return  string   path to the asset file
   */
  protected static function _combine($files)
  {
    // get assets' css config
    $config = Kohana::$config->load('scss');

    // get the most recent modified time of any of the files
    $last_modified = self::_get_last_modified($files);

    // compose the asset filename
    $compiled = md5(implode('|', $files)).'-'.$last_modified.'.css';

    // compose the path to the asset file
    $filename = $config['url'].$compiled;

    // if the file exists no need to generate
    if ( ! file_exists($filename))
    {
      self::_generate_assets($filename, $files);
    }

    return $filename;
  }

  /**
   * Compress the css file
   *
   * @param   string   css string to compress
   * @return  string   compressed css string
   */
  private static function _compress($data)
  {
    $data = preg_replace('~/\*[^*]*\*+([^/][^*]*\*+)*/~', '', $data);
    $data = preg_replace('~\s+~', ' ', $data);
    $data = preg_replace('~ *+([{}+>:;,]) *~', '$1', trim($data));
    $data = str_replace(';}', '}', $data);
    $data = preg_replace('~[^{}]++\{\}~', '', $data);

    return $data;
  }

  /**
   * Check if the asset exists already, if not generate an asset
   *
   * @param string  $file        The filename to check.
   * @param string  $path        The path of the css file.
   * @return  string   path to the asset file
   */
  protected static function _get_filename($file, $path)
  {
    // get the filename
    $filename = str_replace(DOCROOT, '', $file);
    $filename = str_replace('.'.self::$ext, '', $filename);
    $filename = str_replace(DIRECTORY_SEPARATOR, '', $filename);
    $filename = preg_replace('/^.+\//', '', $filename);

    // get the last modified date
    $last_modified = self::_get_last_modified(array($file));

    // compose the expected filename to store in /media/css
    $compiled = $filename.'-'.$last_modified.'.css';

    // compose the expected file path
    $filename = $path.$compiled;

    // if the file exists no need to generate
    if ( ! file_exists($filename))
    {
      touch(DOCROOT . $filename, filemtime($file) - 3600);
    }

    return $filename;
  }

  /**
   * Generate an asset file
   *
   * @param   string   filename of the asset file
   * @param   array    array of source files
   */
  protected static function _generate_assets($filename, $files)
  {
    // create data holder
    $data = '';

    touch( DOCROOT . $filename);

    foreach($files as $file)
    {
      $data .= file_get_contents($file);
    }

    $data = self::_compile($data);
    $data = self::_compress($data);

    file_put_contents(DOCROOT . $filename, $data, LOCK_EX);
  }

  /**
   * Compiles the file from scss to css format
   *
   * @param   string   the scss code to compile
   */
  public static function _compile($data)
  {
    $scss = new scssc();

    try
    {
      $compiled = $scss->compile($data);
    }
    catch (ScssException $ex)
    {
      exit($ex->getMessage());
    }

    return $compiled;
  }

  /**
   * Get the most recent modified date of files
   *
   * @param   array    array of asset files
   * @return  string   path to the asset file
   */
  protected static function _get_last_modified($files)
  {
    $last_modified = 0;

    foreach ($files as $file)
    {
      $modified = filemtime($file);
      if ($modified !== false and $modified > $last_modified) $last_modified = $modified;
    }

    return $last_modified;
  }

  /**
   * Format string to HTML comment format
   *
   * @param   string   string to format
   * @return  string   HTML comment
   */
  protected static function _html_comment($string = '')
  {
    return '<!-- '.$string.' -->';
  }

}