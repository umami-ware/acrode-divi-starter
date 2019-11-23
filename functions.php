<?php
/**** Necessary Code ****/
// https://github.com/YahnisElsts/plugin-update-checker v.4.8.1
require __DIR__ . '/acrode/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://wordpress.acrode.com/wp-update-server/?action=get_metadata&slug=acrode-divi-starter',
	__FILE__, //Full path to the main plugin file or functions.php.
	'acrode-divi-starter'
);
/* acrode theme */
$acCustomTheme = file_exists(ABSPATH . 'wp-content/acrode/themes/acrode-divi-starter');
if ($acCustomTheme) {
	require ABSPATH . 'wp-content/acrode/themes/acrode-divi-starter/acrode/settings.php';
	require ABSPATH . 'wp-content/acrode/themes/acrode-divi-starter/acrode/custom.php';

	if ($acDevelopment) {
		require __DIR__ . '/acrode/development.php';
	}
	if ($acBranding) {
		require __DIR__ . '/acrode/branding.php';
	}
}

function acrode_enqueue()
{
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_script('custom-scripts', get_stylesheet_directory_uri() . '/script.js', array('jquery'));
	// custom files
	global $acCustomTheme;
	if ($acCustomTheme) {
		wp_enqueue_style('parent-style', '/wp-content/acrode/themes/acrode-divi-starter/acrode/style.css');
		wp_enqueue_script('custom-scripts', '/wp-content/acrode/themes/acrode-divi-starter/acrode/script.js', array('jquery'));
	}
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
/* Hide WP version */
function remove_version_info() {
	return '';
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

/* acrode hide editors */
if(!isset($_GET['acAdmin'])) {
	add_action('admin_menu', 'ac_remove_editor_menu', 1);
}

/* acrode theme */
add_shortcode('acrode_html_sitemap', 'acrode_get_html_sitemap');
add_filter('style_loader_tag', 'acrode_codeless_remove_type_attr', 10, 2);
add_filter('script_loader_tag', 'acrode_codeless_remove_type_attr', 10, 2);
add_filter('post_class', 'acrode_remove_hentry_class');
add_filter('the_generator', 'remove_version_info');
add_action('wp_enqueue_scripts', 'acrode_enqueue');