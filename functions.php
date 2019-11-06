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

/**** Necessary Code ****/
/* acrode theme */
function acrode_enqueue()
{
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_script('custom-scripts', get_stylesheet_directory_uri() . '/script.js', array('jquery'));
}
/* acrode remove type from style tags */
function acrode_codeless_remove_type_attr($tag)
{
	return preg_replace('/type=[\'"]text\/(css|javascript)[\'"]/', '', $tag);
}
/* acrode get html sitemap */
function acrode_get_html_sitemap($atts)
{
	$return = '';
	$args = array('public' => 1);
	$ignoreposttypes = array('attachment', 'project');
	$post_types = get_post_types($args, 'objects');
	foreach ($post_types as $post_type) {
		if (!in_array($post_type->name, $ignoreposttypes)) {
			$return .= '<h2>' . $post_type->labels->name . '</h2>';
			$args = array(
				'posts_per_page' => -1,
				'post_type' => $post_type->name,
				'post_status' => 'publish'
			);
			$posts_array = get_posts($args);
			$return .=  '<ul>';
			foreach ($posts_array as $pst) {
				$return .=  '<li><a title="' . $pst->post_title . '" href="' . get_permalink($pst->ID) . '">' . $pst->post_title . '</a></li>';
			}
			$return .=  '</ul>';
		}
	}
	return $return;
}
/* acrode remove hEntry */
function acrode_remove_hentry_class($classes)
{
	$classes = array_diff($classes, array('hentry'));
	return $classes;
}
/* acrode hide editors */
function ac_remove_editor_menu() {
	remove_action('admin_menu', '_add_themes_utility_last', 101);
	remove_submenu_page( 'plugins.php','plugin-editor.php' );
}

/* acrode theme */
add_action('wp_enqueue_scripts', 'acrode_enqueue');
/* acrode set filter for style and script tags */
add_filter('style_loader_tag', 'acrode_codeless_remove_type_attr', 10, 2);
add_filter('script_loader_tag', 'acrode_codeless_remove_type_attr', 10, 2);
/* acrode add html sitemap shortcode */
add_shortcode('acrode_html_sitemap', 'acrode_get_html_sitemap');
/* acrode remove hEntry filter */
add_filter('post_class', 'acrode_remove_hentry_class');

/* acrode hide editors */
if(!isset($_GET['acEnableEditor'])) {
	add_action('admin_menu', 'ac_remove_editor_menu', 1);
}

/* acrode remove pages from search */
if (!is_admin()) {
	function wpb_search_filter($query)
	{
		if ($query->is_search) {
			$query->set('post_type', 'post');
		}
		return $query;
	}
	add_filter('pre_get_posts', 'wpb_search_filter');
}