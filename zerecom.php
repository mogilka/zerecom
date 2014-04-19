<?php

// if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/****************************************************
*
* @File: 	custom_functions.php
* @Package:	GetSimple
* @Action:	Custom functions used by themes
*
*****************************************************/
	
function get_logo_image() {
	global $SITEURL;
	global $TEMPLATE;
	echo trim("<img src=\"".$SITEURL."theme/".$TEMPLATE."/images/logo.png\" alt=\"zere logo\">");
}

function get_company_image() {
	global $SITEURL;
	global $TEMPLATE;
	echo trim("<img src=\"".$SITEURL."theme/".$TEMPLATE."/images/company.png\" alt=\"building company\" width=\"225\">");
}

function get_flags() {
	global $SITEURL;
	global $TEMPLATE;
	$flagkz = "<a href=\"/?setlang=kz\"><img src=\"".$SITEURL."theme/".$TEMPLATE."/images/kz.gif\" alt=\"kz\" title=\"қазақша\"></a>";
	$flagru = "<a href=\"/?setlang=ru\"><img src=\"".$SITEURL."theme/".$TEMPLATE."/images/ru.gif\" alt=\"ru\" title=\"русский\"></a>";
	$flagen = "<a href=\"/?setlang=en\"><img src=\"".$SITEURL."theme/".$TEMPLATE."/images/gb.gif\" alt=\"en\" title=\"english\"></a>";
	echo trim($flagkz.$flagru.$flagen);
}

function get_kazmap() {
	global $SITEURL;
	global $TEMPLATE;
	echo trim("<a href=\"/\"><img src=\"".$SITEURL."theme/".$TEMPLATE."/images/kazmap.png\" alt=\"kazmap\"></a>");
}

function get_envelope() {
	global $SITEURL;
	global $TEMPLATE;
	echo trim("<span class=\"floatright\"><a href=\"/\"><img src=\"".$SITEURL."theme/".$TEMPLATE."/images/email.png\" alt=\"send email\"></a></span>");
}

function get_mailform($userlang) {
//        global $p01contact_settings;
//	$p01contact_settings['lang'] = $userlang;
	echo $p01contact->parse('(% contact %)');
}

/* $i18n['CHMOD_ERROR']; */

?>
