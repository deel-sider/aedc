<?php

if (class_exists('\Textpattern\Tag\Registry')) {
  Txp::get('\Textpattern\Tag\Registry')
    ->register('rvm_css')
    ->register('rvm_css', 'css');
}

if (txpinterface == 'admin')
{
  register_callback('rvm_css_sync', 'css', 'css_save');
  register_callback('rvm_css_sync', 'css', 'css_save_posted');
  register_callback('rvm_css_sync', 'txp.css', 'import');
  register_callback('rvm_css_sync', 'plugin_lifecycle.rvm_css', 'enabled');
  register_callback('rvm_css_setup', 'plugin_lifecycle.rvm_css', 'installed');
  register_callback('rvm_css_cleanup', 'plugin_lifecycle.rvm_css', 'deleted');
}


function rvm_css($atts)
{
  global $doctype, $pretext;

  extract(lAtts(array(
    'format' => 'url',
    'media'  => 'screen',
    'name'   => $pretext['css'],
    'rel'    => 'stylesheet',
    'theme'  => isset($pretext['skin']) ? $pretext['skin'] : '',
    'title'  => '',
  ), $atts));

  if ($name === '')
  {
      $name = 'default';
  }

  if ($format === 'link' and strpos($name, ',') !== false)
  {
    $names = do_list($name);
    $css = '';

    foreach ($names as $name)
    {
      $atts['name'] = $name;
      $css .= rvm_css($atts);
    }

    return $css;
  }

  if ($theme)
  {
    $skindir = strtolower(sanitizeForUrl($theme)).'/';
  }

  $file = get_pref('rvm_css_dir').'/'.$skindir.strtolower(sanitizeForUrl($name)).'.css';

  if (empty(get_pref('rvm_css_dir')) or
    !is_readable(get_pref('path_to_site').'/'.$file) and
    (!rvm_css_sync() or !is_readable(get_pref('path_to_site').'/'.$file))
  )
  {
    $atts['name'] = $name;

    return css($atts);
  }


  if ($format == 'link') {
    return tag_void('link', array(
      'rel'   => $rel,
      'type'  => $doctype != 'html5' ? 'text/css' : '',
      'media' => $media,
      'title' => $title,
      'href'  => hu.$file,
    ));
  }

  return hu.$file;
}


function rvm_css_sync()
{
  if (!get_pref('rvm_css_dir'))
  {
    return false;
  }

  $basedir = get_pref('path_to_site').'/'.get_pref('rvm_css_dir').'/';

  if (!file_exists($basedir))
  {
    mkdir($basedir, 0755);
  }
  elseif (!is_writable($basedir))
  {
    return false;
  }

  if ($rs = safe_rows_start('*', 'txp_css', '1=1'))
  {
    while ($row = nextRow($rs))
    {
      extract($row);

      if (preg_match('!^[a-zA-Z0-9/+]*={0,2}$!', $css))
      {
        $css = base64_decode($css);
      }

      if (!empty($skin))
      {
        $skin = strtolower(sanitizeForUrl($skin));

        if (!file_exists($basedir.$skin))
        {
          mkdir($basedir.$skin, 0755);
        }
        elseif (!is_writable($basedir.$skin))
        {
          continue;
        }

        $skindir = $skin.'/';
      }
      else
      {
        $skindir = '';
      }

      $basefile = $basedir.$skindir.strtolower(sanitizeForUrl($name));
      $cssfile  = $basefile.'.css';

      if (file_exists($cssfile) and !is_writable($cssfile))
      {
        continue;
      }

      if (class_exists('lessc'))
      {
        $handle = fopen($file.'.less', 'wb');
        fwrite($handle, $css);
        fclose($handle);
        chmod($file.'.less', 0644);

        $less = new lessc();
        $less->setFormatter('compressed');
        $less->setImportDir(get_pref('path_to_site').'/'.get_pref('rvm_css_dir').'/');

        try
        {
          $css  = $less->parse($css);
        }
        catch (Exception $ex)
        {
          error_log("lessphp fatal error: ".$ex->getMessage());
          return false;
        }
      }

      file_put_contents($cssfile, $css) and chmod($cssfile, 0644);
    }
  }

  return true;
}


function rvm_css_setup()
{
  if (!get_pref('rvm_css_dir'))
  {
    set_pref('rvm_css_dir', 'css', 'admin', '1', 'text_input', '20');
  }
}


function rvm_css_cleanup()
{
    safe_delete('txp_prefs', "name='rvm_css_dir'");
    safe_delete('txp_lang', "owner='rvm_css'");
}
