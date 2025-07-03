<?php

// https://github.com/YahnisElsts/plugin-update-checker v.4.9
require __DIR__ . '/acrode/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/umami-ware/acrode-divi-starter',
	__FILE__, //Full path to the main plugin file or functions.php.
	'acrode-divi-starter'
);
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

$myUpdateChecker->setBranch('stable');

// Settings
/*$acDevelopment = [
	'releaseMonth' => 1,
	'releaseDay' => 1,
	'releaseYear' => 2020,
	'acColorOne' => '#ff0000',
	'acColorTwo' => '#00ff00',
	'hideTimer' => false,
	'h2Text' => '',
	'viewSiteKey' => 'ac2020'
];*/
$acDevelopment = false;
$acMultisiteSiteConfigs = false;
$acBrandingFrontend = true;
/* Theme */
$acCustomTheme = is_dir(ABSPATH . 'wp-content/acrode/acrode-divi-starter/acrode');
if ($acCustomTheme) {
	include ABSPATH . 'wp-content/acrode/acrode-divi-starter/acrode/settings.php';
	include ABSPATH . 'wp-content/acrode/acrode-divi-starter/acrode/custom.php';
	$acMultisiteSiteConfigs = is_multisite() && $acMultisiteSiteConfigs;
	if ($acMultisiteSiteConfigs) {
		include ABSPATH . 'wp-content/acrode/acrode-divi-starter/acrode/' . get_current_blog_id() . '/settings.php';
		include ABSPATH . 'wp-content/acrode/acrode-divi-starter/acrode/' . get_current_blog_id() . '/custom.php';
	}
}
if ($acDevelopment) {
	require __DIR__ . '/acrode/development.php';
}
function acrode_enqueue()
{
	wp_enqueue_style('theme-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_script('theme-scripts', get_stylesheet_directory_uri() . '/script.js', array('jquery'));
	// Custom files
	global $acCustomTheme;
	if ($acCustomTheme) {
		wp_enqueue_style('custom-style', '/wp-content/acrode/acrode-divi-starter/acrode/style.css');
		wp_enqueue_script('custom-scripts', '/wp-content/acrode/acrode-divi-starter/acrode/script.js', array('jquery'));
	}

	global $acMultisiteSiteConfigs;
	if ($acMultisiteSiteConfigs) {
		wp_enqueue_style('custom-style-current-site', '/wp-content/acrode/acrode-divi-starter/acrode/' . get_current_blog_id() . '/style.css');
		wp_enqueue_script('custom-scripts-current-site', '/wp-content/acrode/acrode-divi-starter/acrode/' . get_current_blog_id() . '/script.js', array('jquery'));
	}
}
/* Remove type from style tags */
function acrode_codeless_remove_type_attr($tag)
{
	return preg_replace('/type=[\'"]text\/(css|javascript)[\'"]/', '', $tag);
}
/* Get html sitemap */
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
/* Remove hEntry */
function acrode_remove_hentry_class($classes)
{
	$classes = array_diff($classes, array('hentry'));
	return $classes;
}
/* Hide editors */
function acrode_remove_editor_menu()
{
	remove_action('admin_menu', '_add_themes_utility_last', 101);
	remove_submenu_page('plugins.php', 'plugin-editor.php');
}
/* Hide WP version */
function acrode_remove_version_info()
{
	return '';
}
function acrode_frontend_backlink()
{
?>
	<style>
		body {
			position: relative;
		}

		#acrode-wpauthor {
			position: absolute;
			left: 3px;
			bottom: 3px;
			height: 21px;
			width: 21px;
		}
	</style>
	<a id="acrode-wpauthor" href="https://acrode.com" target="_blank" title="WordPress Theme Author"><img width="21px" src="/wp-content/themes/acrode-divi-starter/img/acrode.svg"></a>
<?php
}
/* Override default footer */
function et_get_original_footer_credits()
{
	return sprintf(__('Designed by %1$s | Powered by %2$s', 'Divi'), '<a href="https://acrode.com" title="acrode | Digital Engineers" target="_blank">acrode</a>', '<a href="http://www.wordpress.org">WordPress</a>');
}
/* Hide login errors */
function no_wordpress_errors()
{
	return '&#9888; ' . __('Invalid');
}
if ($acBrandingFrontend) {
	add_action('wp_footer', 'acrode_frontend_backlink');
}
/* Remove pages from search */
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


function login_function()
{
	add_filter('gettext', 'username_change', 20, 3);
	function username_change($translated_text, $text)
	{
		if ($text === 'Username or Email Address') {
			$translated_text = __('Username');
		}
		return $translated_text;
	}
}
add_action('login_head', 'login_function');

/* Theme */
add_shortcode('acrode_html_sitemap', 'acrode_get_html_sitemap');
add_filter('login_errors', 'no_wordpress_errors');
add_filter('style_loader_tag', 'acrode_codeless_remove_type_attr', 10, 2);
add_filter('script_loader_tag', 'acrode_codeless_remove_type_attr', 10, 2);
add_filter('post_class', 'acrode_remove_hentry_class');
add_filter('the_generator', 'acrode_remove_version_info');
add_action('wp_enqueue_scripts', 'acrode_enqueue');
add_action('login_head', 'login_function');
remove_filter('authenticate', 'wp_authenticate_email_password', 20);
remove_action('welcome_panel', 'wp_welcome_panel');
