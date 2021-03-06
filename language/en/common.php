<?php

/**
* phpBB Extension - marttiphpbb customcode
* @copyright (c) 2014 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [

	'MARTTIPHPBB_CUSTOMCODE_LINK_EDIT_TITLE'				=> 'Edit custom code for this location.',
	'MARTTIPHPBB_CUSTOMCODE_HIDE'							=> 'Hide',
	'MARTTIPHPBB_CUSTOMCODE_HIDE_TEMPLATE_EVENTS_LOCATIONS'	=> 'Hide Custom Code template events locations',
	'MARTTIPHPBB_CUSTOMCODE_INSIDE_HTML_HEAD'				=> '(inside html head)',
	'MARTTIPHPBB_CUSTOMCODE_GITHUB_LINK'					=> '%1$sCustom Code Github link%2$s%3$sCustom Code%4$s extension for phpBB',
		// MARTTIPHPBB_CUSTOMCODE_GITHUB_LINK: This is the example github link in the footer to be loaded on installation.
		// Between %1$s and %2$s is a html comment. Between %3$s and %4$s is the link to the Custom Code repository on Github.
]);
