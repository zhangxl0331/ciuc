<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function formhash() {
	return substr(md5(substr(time(), 0, -4).UC_KEY), 16);
}

function submitcheck() {
	return @getgpc('formhash', 'P') == formhash() ? true : false;
}

function getgpc($k, $t='GP') {
	$t = strtoupper($t);
	switch($t) {
		case 'GP' : isset($_POST[$k]) ? $var = &$_POST : $var = &$_GET; break;
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		case 'R': $var = &$_REQUEST; break;
	}
	return isset($var[$k]) ? $var[$k] : '';
}
