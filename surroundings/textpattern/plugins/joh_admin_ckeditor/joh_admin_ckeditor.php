<?php
register_callback('joh_admin_ckeditor','article');

function joh_admin_ckeditor()
{
  $out = '<script type="text/javascript" src="ckeditor/ckeditor.js"></script>'
         . '<script type="text/javascript">'
         . "// <![CDATA[\n"
/* Loading different settings-variables for body and excerpt. Customize in markitup/sets/textile/set.js */
         . "CKEDITOR.replace('body'); \n"
         . "CKEDITOR.replace('excerpt'); \n"
         . '//]]>'
         . '</script>';
  echo $out;
}
