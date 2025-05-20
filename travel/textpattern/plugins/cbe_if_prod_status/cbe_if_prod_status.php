<?php
/*
** cbe_if_prod_status
** Client-side textpattern plugin
** Automatically sets noindex directive if site is not live
** Outputs or does something for a given production status, with <txp:else /> support
** Claire Brione - http://www.clairebrione.com/
**
** 0.1   - 16 Jul 2012 - Initial release
** 0.1.1 - 28 Jan 2013 - Fix: didn't output anything if site status == 'live'
** 0.2   - 22 Jul 2013 - Neatly rewritten by Jukka Svahn (Gocom) http://forum.textpattern.com/viewtopic.php?pid=274178#p274178
**
 ************************************************************************/

/**************************************************
 **
 ** Automatism
 **
 **************************************************/

/**
 * Registers robots meta injector when site is in testing or debugging mode.
 */
if (get_pref('production_status') !== 'live')
{
    register_callback( 'cbe_meta_prod_status', 'textpattern_end' );
}

/**
 * Replaces the current output buffer contents.
 */
function cbe_meta_prod_status()
{
    if ($ob = ob_get_contents())
    {
        ob_clean();
        echo str_replace('<head>', '<head>'.n.'<meta name="robots" content="noindex, nofollow" />', $ob);
    }
}

/**************************************************
 **
 ** Available tags
 **
 **************************************************/

/**
 * Tests the production status.
 *
 * @param array  $atts  Attributes
 * @param string $thing Contained statement
 */
function cbe_if_prod_status( $atts, $thing )
{
    extract(lAtts(array(
         'value' => 'debug, testing',
    ), $atts));

    return parse( EvalElse( $thing, in_list( get_pref( 'production_status' ), $value ) ) ) ;
}
