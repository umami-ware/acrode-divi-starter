<?php
/**** Optional: Development Mode ****/
$GLOBALS['acViewSiteKey'] = 'ac2019';
$GLOBALS['acReleaseTag'] = 1;
$GLOBALS['acReleaseMonat'] = 1;
$GLOBALS['acReleaseJahr'] = 2020;
$GLOBALS['hideTimer'] = false;
$GLOBALS['h2Text'] = 'Die Webseite ist aktuell noch in der Entwicklung';
$GLOBALS['acColorOne'] = '#29a6f6';
$GLOBALS['acColorTwo'] = '#6251d6';
include_once(__DIR__ . '/includes/ac_development.php');

/**** Optional: Branding ****/
include_once(__DIR__ . '/includes/ac_branding.php');

/**** Optional: Add global Divi module anywhere ****/
function acrode_show_gdivi_module_shortcode($gdId)
{
	extract(shortcode_atts(array('id' => '*'), $gdId));
	return do_shortcode('[et_pb_section global_module="' . $id . '"][/et_pb_section]');
}
add_shortcode('acrode_gdivi_module', 'acrode_show_gdivi_module_shortcode');