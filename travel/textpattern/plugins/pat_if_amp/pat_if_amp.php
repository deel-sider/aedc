<?php
/**
 * pat_if_amp plugin. Support for Google AMP with Textpattern CMS.
 * @author:  Michael K Pate & Patrick LEFEVRE.
 * @link:    https://github.com/cara-tm/pat_if_amp, https://github.com/michaelkpate/mkp_if_amp
 * @type:    Public
 * @prefs:   no
 * @order:   5
 * @version: 3.6
 * @license: GPLv2
*/

/**
 * This plugin tag registry.
 */
if (class_exists('\Textpattern\Tag\Registry')) {
	Txp::get('\Textpattern\Tag\Registry')
		->register('pat_if_amp')
		->register('pat_amp_sanitize')
		->register('pat_amp_redirect');
}

/**
 * Register callback when public pages are rendering.
 *
 */
if (txpinterface === 'public') {
	// Loads a callback with init function for public context.
	register_callback('pat_if_amp_init', 'textpattern');
}

/**
 * Init function which create the mkp_variable.
 *
 * @param
 * @return boolean $variable
 */
function pat_if_amp_init()
{
	global $variable;

	// Initiates a TXP variable which sniffs for 'amp' (with or without a final backslash) in URLs or a simple query '?amp'
	$variable['pat_amp'] = (preg_match( '/amp/',  $GLOBALS['pretext']['request_uri'] ) || gps('amp') ? 1 : 0 );
}

/**
 * Main plugin function.
 *
 * @param  $atts   string This plugin attributes
 * @param  $thing  string
 * @return string
 */
function pat_if_amp($atts, $thing='')
{
	global $variable;

	extract(lAtts(array(
		'redirect'  => false,
		'url'       => hu,
		'subdomain' => 'amp',
		'permlink'  => true,
	), $atts));

	$path = parse_url($GLOBALS['pretext']['request_uri'], PHP_URL_PATH);
	$els = explode('/', $path);

	// Splits URL parts
	$parts = explode( '/', preg_replace("|^https?://[^/]+|i", "", $GLOBALS['pretext']['request_uri']), count($els));

	if ($redirect && '1' == $variable['pat_amp']) {
		// Redirect to same article's title within the subdomain.
		pat_amp_redirect(array('url'=>$url,'subdomain'=>$subdomain,'permlink'=>$permlink));
	} else {
		// If the URL ends in 'amp' this will return true; otherwise false.
		return (end($parts) == 'amp') ? parse(EvalElse($thing, true)) : parse(EvalElse($thing, false));
	}
}

/**
 * Sanitize all inline CSS styles within body/excerpt text content.
 *
 * @param:  $atts array Plugin attribute
 * @return: string      Text content
 */
function pat_amp_sanitize($atts)
{
	extract(lAtts(array(
		'content' => 'body',
	), $atts));

	$out = '';

	if (in_array($content, array('body', 'excerpt')) ) {
		$out = preg_replace('/(<[^>]+) style=".*?"/i','$1', $content());
	} else {
		$out = trigger_error( gTxt('invalid_attribute_value', array('{name}' => 'content')), E_USER_WARNING );;
	}

	return $out;
}

/**
 * Extracts the domain name and redirects to a subdomain.
 *
 * @param: $atts array Plugin attribute
 * @return redirection or false
 */
function pat_amp_redirect($atts)
{
	global $pretext, $thisarticle;

	extract(lAtts(array(
		'url'       => hu,
		'subdomain' => 'amp',
		'permlink'  => false,
	), $atts));

	// Array of the URL.
	$parts = parse_url($url);
	// Verify the host.
	$domain = isset($parts['host']) ? $parts['host'] : '';

	// Regex for a well spelling domain name.
	if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $matches) ) {
		// Redirects to subdomain without or with the current article's URL title.
		return header('Location: '.$parts['scheme'].'://'.$subdomain.'.'.$matches['domain'].($permlink ? '/'.str_replace(hu, '', permlinkurl($thisarticle)) : ''));
	}

	// Otherwise, do nothing.
	return false;
}