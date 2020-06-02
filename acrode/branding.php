<?php
class AcrodeBrandingFilters
{
	public static $acTheme = 'Acrode Builder';
	public static $acThemeIcon = '/wp-content/themes/acrode-divi-starter/img/acrode.svg';
	public static $acThemeLogo = '/wp-content/themes/acrode-divi-starter/img/acrode-full.svg';

	public static function filterAdminBarMenu($admin_bar)
	{
		$visualBuilderNode = $admin_bar->get_node('et-use-visual-builder');
		if (!empty($visualBuilderNode)) {
			$admin_bar->remove_node('et-use-visual-builder');
			$visualBuilderNode = get_object_vars($visualBuilderNode);
			$visualBuilderNode['id'] = 'ac-et-use-visual-builder';
			$admin_bar->add_node($visualBuilderNode);
		}
	}

	public static function filterTranslatedTextDiviOnly($text)
	{
		return str_replace('Divi', self::$acTheme, $text);
	}

	public static function filterTranslatedText($translated)
	{
		return preg_replace('/(Divi\b)/', self::$acTheme, $translated);
	}

	public static function builderScript()
	{
		// Script to reset the export file name
?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Following code from WP and Divi Icons Pro (fb.js) - modified

				var MO = window.MutationObserver ? window.MutationObserver : window.WebkitMutationObserver;
				var fbApp = document.getElementById('et-fb-app');
				if (!fbApp) {
					fbApp = document.body;
				}

				if (MO && fbApp) {
					(new MO(function(events) {
						$.each(events, function(i, event) {
							if (event.addedNodes && event.addedNodes.length) {
								$.each(event.addedNodes, function(i, node) {
									var $exportFileNameField = $(node).find('#et-fb-exportFileName');
									if ($exportFileNameField.length) {
										$exportFileNameField.val($exportFileNameField.val().replace('<?php echo (addslashes('Divi')); ?>', '<?php echo (addslashes(self::$acTheme)); ?>'));
									}
								});
							}
						});
					})).observe(fbApp, {
						childList: true,
						subtree: true
					});
				}

				// End code from WP and Divi Icons Pro
			});
		</script>
	<?php
	}

	public static function fbAssets()
	{
		// "Translate" frontend builder strings
		if (has_action('et_fb_enqueue_assets', 'et_fb_backend_helpers') && function_exists('et_fb_backend_helpers')) {
			remove_action('et_fb_enqueue_assets', 'et_fb_backend_helpers');
			add_filter('gettext', array('AcrodeBrandingFilters', 'filterTranslatedTextDiviOnly'));
			add_filter('ngettext', array('AcrodeBrandingFilters', 'filterTranslatedTextDiviOnly'));
			et_fb_backend_helpers();
			remove_filter('gettext', array('AcrodeBrandingFilters', 'filterTranslatedTextDiviOnly'));
			remove_filter('ngettext', array('AcrodeBrandingFilters', 'filterTranslatedTextDiviOnly'));
		}
		add_action('wp_footer', array('AcrodeBrandingFilters', 'builderScript'), 999);
	}

	public static function filterPostStates($states)
	{
		$diviIndex = array_search('Divi', $states);
		if ($diviIndex !== false) {
			$states[$diviIndex] = self::$acTheme;
		}
		return $states;
	}

	public static function modifyAdminMenu()
	{
		global $menu;

		foreach ($menu as $key => $item) {
			if ($item[0] === 'Divi') {
				$menu[$key][0] = self::$acTheme;
			} else if (!in_array($item[2], array('index.php', 'edit.php', 'edit.php?post_type=page', 'users.php', 'edit.php?post_type=project', 'upload.php', 'tools.php', 'options-general.php', 'edit-comments.php'))) {
				remove_menu_page($item[2]);
			}
		}
		// Remove Divi Support Page
		remove_submenu_page('options-general.php', 'wprocket');
		remove_submenu_page('et_divi_options', 'et_support_center_divi');
	}

	public static function modifyAdminMenuNetwork()
	{
		global $menu;

		foreach ($menu as $key => $item) {
			if (!in_array($item[2], array('index.php', 'users.php'))) {
				remove_menu_page($item[2]);
			}
		}
	}

	public static function customAdminbar($wp_admin_bar)
	{
		$wp_admin_bar->remove_node('comments');
		$wp_admin_bar->remove_node('kinsta-cache');

		$user_data         = wp_get_current_user();
		$user_display_name = isset($user_data->display_name) ? $user_data->display_name : false;
		$user_id           = isset($user_data->ID) ? (int) $user_data->ID : 0;
		if (!$user_id || !$user_display_name) {
			return;
		}
		$user_avatar = get_avatar($user_id, 26);  // translators: %s: Current user's display name
		$my_account_text = sprintf(
			__('Hey, %s'),
			'<span class="display-name">' . esc_html($user_data->display_name) . '</span>'
		);
		$wp_admin_bar->add_node(
			array(
				'id'    => 'my-account',
				'title' => $my_account_text . $user_avatar,
			)
		);
	}


	public static function filterPortabilityArgs($args)
	{
		$args->view = true;
		return $args;
	}

	public static function addTranslateFilters()
	{
		add_filter('gettext', array('AcrodeBrandingFilters', 'filterTranslatedText'));
		add_filter('ngettext', array('AcrodeBrandingFilters', 'filterTranslatedText'));
	}

	public static function removeTranslateFilters()
	{
		remove_filter('gettext', array('AcrodeBrandingFilters', 'filterTranslatedText'));
		remove_filter('ngettext', array('AcrodeBrandingFilters', 'filterTranslatedText'));
	}

	public static function filterWpOption($optionValue, $optionName)
	{
		switch ($optionName) {
			case 'et_bfb_settings':
				$optionValue['enable_bfb'] = 'on';
				break;
		}
		return $optionValue;
	}

	public static function adminCssJs()
	{
	?>
		<style>
			#wp-admin-bar-wp-logo {
				pointer-events: none;
			}

			#wpadminbar #wp-admin-bar-wp-logo>.ab-item .ab-icon:before {
				background-image: url(<?php echo self::$acThemeIcon ?>) !important;
				background-position: 50% 50%;
				color: rgba(0, 0, 0, 0);
				background-size: contain;
				background-repeat: no-repeat;
				color: rgba(0, 0, 0, 0);
			}

			#wpadminbar #wp-admin-bar-wp-logo.hover>.ab-item .ab-icon {
				background-position: 0 0;
			}

			#wp-admin-bar-wp-rocket,
			#wp-version-message {
				display: none !important;
			}

			#adminmenu #toplevel_page_et_divi_options div.wp-menu-image::before,
			#adminmenu #toplevel_page_et_divi_100_options div.wp-menu-image::before {
				background: url(<?php echo self::$acThemeIcon ?>) no-repeat !important;
				content: '' !important;
				margin-top: 6px !important;
				max-width: 22px !important;
				max-height: 22px !important;
				width: 100%;
				background-size: contain !important;
			}

			#et_pb_layout .hndle:before,
			#et_pb_toggle_builder:before {
				color: transparent !important;
				background: url(<?php echo self::$acThemeIcon ?>) no-repeat !important;
				background-size: contain !important;
				max-height: 33px;
				max-width: 36px;
				width: 100%;
			}

			#et_pb_layout h3:before {
				background-image: url(<?php echo self::$acThemeIcon ?>) no-repeat !important;
			}

			#et_settings_meta_box .hndle.ui-sortable-handle::before {
				color: transparent !important;
				background: url(<?php echo self::$acThemeIcon ?>) no-repeat !important;
				max-height: 26px;
				max-width: 26px;
				margin: 9px 0px 0px 0px;
				background-size: contain !important;
			}

			#et_settings_meta_box .hndle:before {
				color: transparent !important;
				background: url(<?php echo self::$acThemeIcon ?>) no-repeat !important;
				height: 36px;
				width: 36px;
				margin: 6px 0px 0px 0px;
			}

			#epanel-logo {
				content: url(<?php echo self::$acThemeIcon ?>) !important;
				width: 143px;
				height: 65px;
			}

			.toplevel_page_et_divi_options #epanel-header {
				display: none;
			}

			#epanel-title {
				background-color: transparent !important;
			}

			#epanel-title:before {
				display: none !important;
			}

			.divi-ghoster-placeholder-block-icon {
				background: url(<?php echo self::$acThemeIcon ?>) no-repeat;
				background-size: contain;
				background-position: left 0px center;
			}

			.wp-block-divi-placeholder .et-icon:before {
				background: url(<?php echo self::$acThemeIcon ?>) no-repeat;
				content: '' !important;
				width: 50px;
				height: 50px;
				margin-left: auto;
				margin-right: auto;
				background-size: contain;
				background-position: left 0px center;
			}

			.editor-post-switch-to-divi:after {
				background: url(<?php echo self::$acThemeIcon ?>) no-repeat;
				content: '' !important;
				width: 32px;
				height: 32px;
				margin-top: -4px;
				margin-left: -5px;
				background-size: contain;
				background-position: left 0px center;
			}

			.et_pb_roles_title:before,
			.et-tb-admin-container-header:before {
				background: url(<?php echo self::$acThemeIcon ?>) no-repeat !important;
				content: '' !important;
				width: 32px !important;
				height: 32px !important;
				background-size: contain !important;
				background-position: left 0px center !important;
			}

			/* Hide Child Theme notice */
			.theme-browser .parent-theme {
				display: none;
			}
		</style>
<?php
	}

	public static function filterBuilderHelpVideos()
	{
		return array();
	}

	public static function customLoginLogo()
	{
		$logo_style = '.login h1 a { background-image: url(' . esc_url(self::$acThemeLogo) . '); background-size: 100% auto; width: 100%; }';
		wp_add_inline_style('login', $logo_style);
	}

	public static function customLoginLogoUrl()
	{
		return esc_url(home_url());
	}

	public static function customLoginLogoTitle()
	{
		return esc_html__(get_the_title(), 'et-text-domain');
	}

	/*
			public static function onThemeSetUp()
			{
				global $themename;
				$themename = self::$acTheme;
			}
			*/

	/*public static function kill_divi($themes)
	{
		unset($themes['Divi']);
		return $themes;
	}*/

	/*public static function hide_plugins_network($plugins)
	{
		if (in_array('wp-rocket/wp-rocket.php', array_keys($plugins))) {
			unset($plugins['akismet/akismet.php']);
		}
		return $plugins;
	}*/

	/*public static function hide_plugins()
	{
		global $wp_list_table;
		$hidearr = array('wp-rocket/wp-rocket.php');
		$myplugins = $wp_list_table->items;
		foreach ($myplugins as $key => $val) {
			if (in_array($key, $hidearr)) {
				unset($wp_list_table->items[$key]);
			}
		}
	}*/

	public static function removeFooterAdmin()
	{
		echo '<span id="footer-thankyou">Created by <a href="https://acrode.com" target="_blank">Acrode</a>. Thanks for your trust!</span>';
	}

	/**
	 * Add theme info widget into WordPress Dashboard
	 */
	public static function setupDashboardWidgets()
	{
		remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');

		remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
		remove_meta_box('dashboard_primary', 'dashboard', 'side');
		remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
		remove_meta_box('dashboard_activity', 'dashboard', 'normal');
		remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');
		remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

		wp_add_dashboard_widget(
			'et_dashboard_widget_info',
			esc_html__('Theme Details', 'et-text-domain'),
			array('AcrodeBrandingFilters', 'dashboardWidgetInfoRender')
		);
	}
	/**
	 * Render the content of theme info widget
	 */
	public static function dashboardWidgetInfoRender()
	{
		$content = __('
		<a href="https://acrode.com/" title="Acrode" target="_blank"><img width="50%" style="max-width: 270px" src="' . self::$acThemeLogo . '" alt="Acrode" /></a>
	  <ul>
		<li>
		  <strong>Created By:</strong> Acrode
		</li>
		<li>
		  <strong>Website:</strong> <a title="Acrode" href="https://acrode.com/">acrode.com/</a>
		</li>
		<li>
		  <strong>Contact:</strong> <a title="Acrode" href="https://acrode.com/#contact">Let\'s get in contact!</a>
		</li>
		<li>
		  <strong>Support:</strong> <a title="Acrode Support" href="mailto:support@acrode.com?subject=Support: ' . get_site_url() . '">support@acrode.com</a>
		</li>
	  </ul>', 'et-text-domain');
		echo wp_kses_post($content);
	}

	public static function setup()
	{
		// TODO: Fix language Bug on name change
		//add_action('after_setup_theme', array('AcrodeBrandingFilters', 'onThemeSetUp'), 9999);

		add_action('admin_bar_menu', array('AcrodeBrandingFilters', 'filterAdminBarMenu'), 9999);
		add_action('et_fb_enqueue_assets', array('AcrodeBrandingFilters', 'fbAssets'), 9);

		add_filter('option_et_bfb_settings', array('AcrodeBrandingFilters', 'filterWpOption'), 10, 2);
		add_filter('et_fb_help_videos', array('AcrodeBrandingFilters', 'filterBuilderHelpVideos'));

		add_action('admin_bar_menu', array('AcrodeBrandingFilters', 'customAdminbar'), 9999);

		if (is_admin()) {
			add_action('wp_dashboard_setup', array('AcrodeBrandingFilters', 'setupDashboardWidgets'));
			add_filter('admin_footer_text', array('AcrodeBrandingFilters', 'removeFooterAdmin'), 9999);
			add_action('admin_menu', array('AcrodeBrandingFilters', 'modifyAdminMenu'), 9999);
			if (!is_multisite()) {
				add_action('network_admin_menu', array('AcrodeBrandingFilters', 'modifyAdminMenuNetwork'), 9999);
			}
			add_action('admin_head', array('AcrodeBrandingFilters', 'adminCssJs'));

			add_action('et_pb_before_page_builder', array('AcrodeBrandingFilters', 'builderScript'));

			add_filter('display_post_states', array('AcrodeBrandingFilters', 'filterPostStates'), 9999);
			self::addTranslateFilters();

			// Remove the translate filters before the plugins list (will be restored after the plugins list)
			global $pagenow;
			if ((isset($pagenow) && $pagenow == 'plugins.php')) {
				add_action('load-plugins.php', array('AcrodeBrandingFilters', 'removeTranslateFilters'));
				add_action('admin_enqueue_scripts', array('AcrodeBrandingFilters', 'addTranslateFilters'));
			}
			// Show portability button on Theme Options page
			if (isset($_GET['page']) && $_GET['page'] == 'et_divi_options') {
				add_filter('et_core_portability_args_epanel', array('AcrodeBrandingFilters', 'filterPortabilityArgs'));
			}
		}

		// White label Login
		add_action('login_enqueue_scripts', array('AcrodeBrandingFilters', 'customLoginLogo'));
		add_filter('login_headerurl', array('AcrodeBrandingFilters', 'customLoginLogoUrl'));
		add_filter('login_headertext', array('AcrodeBrandingFilters', 'customLoginLogoTitle'));

		// Kill Divi and plugins
		/*if (is_admin()) {
			add_filter('wp_prepare_themes_for_js', array('AcrodeBrandingFilters', 'kill_divi'));

			if (is_multisite()) {
				add_filter('all_plugins', array('AcrodeBrandingFilters', 'hide_plugins_network'));
			} else {
				add_action('pre_current_active_plugins', array('AcrodeBrandingFilters', 'hide_plugins'));
			}
		}*/
	}
}

AcrodeBrandingFilters::setup();
