<?php
// Условный тэг для определения зашел ли полбзователь с мобильного устройства или с большого брата
// Функция определения от Andy Moore - dotMobi Certified Mobile Web Developer http://www.andymoore.info/
// Я только завернул её в плагин
function act_if_mobile($attr, $thing){
	if ( detect_mobile_device() ) {
		return parse( EvalElse( $thing, true ) );
	}
	return parse( EvalElse( $thing, false ) );
}

function detect_mobile_device(){

	// check if the user agent value claims to be windows but not windows mobile
	if(stristr($_SERVER['HTTP_USER_AGENT'],'windows')&&!stristr($_SERVER['HTTP_USER_AGENT'],'windows ce')){
		return false;
	}
	// check if the user agent gives away any tell tale signs it's a mobile browser
	if(eregi('up.browser|up.link|windows ce|iemobile|mini|mmp|symbian|midp|wap|phone|pocket|mobile|pda|psp',$_SERVER['HTTP_USER_AGENT'])){
		return true;
	}
	// check the http accept header to see if wap.wml or wap.xhtml support is claimed
	if(stristr($_SERVER['HTTP_ACCEPT'],'text/vnd.wap.wml')||stristr($_SERVER['HTTP_ACCEPT'],'application/vnd.wap.xhtml+xml')){
		return true;
	}
	// check if there are any tell tales signs it's a mobile device from the _server headers
	if(isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])||isset($_SERVER['X-OperaMini-Features'])||isset($_SERVER['UA-pixels'])){
		return true;
	}
	// build an array with the first four characters from the most common mobile user agents
	$a = array(
		'acs-'=>'acs-',
		'alav'=>'alav',
		'alca'=>'alca',
		'amoi'=>'amoi',
		'audi'=>'audi',
		'aste'=>'aste',
		'avan'=>'avan',
		'benq'=>'benq',
		'bird'=>'bird',
		'blac'=>'blac',
		'blaz'=>'blaz',
		'brew'=>'brew',
		'cell'=>'cell',
		'cldc'=>'cldc',
		'cmd-'=>'cmd-',
		'dang'=>'dang',
		'doco'=>'doco',
		'eric'=>'eric',
		'hipt'=>'hipt',
		'inno'=>'inno',
		'ipaq'=>'ipaq',
		'java'=>'java',
		'jigs'=>'jigs',
		'kddi'=>'kddi',
		'keji'=>'keji',
		'leno'=>'leno',
		'lg-c'=>'lg-c',
		'lg-d'=>'lg-d',
		'lg-g'=>'lg-g',
		'lge-'=>'lge-',
		'maui'=>'maui',
		'maxo'=>'maxo',
		'midp'=>'midp',
		'mits'=>'mits',
		'mmef'=>'mmef',
		'mobi'=>'mobi',
		'mot-'=>'mot-',
		'moto'=>'moto',
		'mwbp'=>'mwbp',
		'nec-'=>'nec-',
		'newt'=>'newt',
		'noki'=>'noki',
		'opwv'=>'opwv',
		'palm'=>'palm',
		'pana'=>'pana',
		'pant'=>'pant',
		'pdxg'=>'pdxg',
		'phil'=>'phil',
		'play'=>'play',
		'pluc'=>'pluc',
		'port'=>'port',
		'prox'=>'prox',
		'qtek'=>'qtek',
		'qwap'=>'qwap',
		'sage'=>'sage',
		'sams'=>'sams',
		'sany'=>'sany',
		'sch-'=>'sch-',
		'sec-'=>'sec-',
		'send'=>'send',
		'seri'=>'seri',
		'sgh-'=>'sgh-',
		'shar'=>'shar',
		'sie-'=>'sie-',
		'siem'=>'siem',
		'smal'=>'smal',
		'smar'=>'smar',
		'sony'=>'sony',
		'sph-'=>'sph-',
		'symb'=>'symb',
		't-mo'=>'t-mo',
		'teli'=>'teli',
		'tim-'=>'tim-',
		'tosh'=>'tosh',
		'treo'=>'treo',
		'tsm-'=>'tsm-',
		'upg1'=>'upg1',
		'upsi'=>'upsi',
		'vk-v'=>'vk-v',
		'voda'=>'voda',
		'wap-'=>'wap-',
		'wapa'=>'wapa',
		'wapi'=>'wapi',
		'wapp'=>'wapp',
		'wapr'=>'wapr',
		'webc'=>'webc',
		'winw'=>'winw',
		'winw'=>'winw',
		'xda-'=>'xda-'
	);
	// check if the first four characters of the current user agent are set as a key in the array
	if(isset($a[substr($_SERVER['HTTP_USER_AGENT'],0,4)])){
		return true;
	}
}