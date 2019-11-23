<?php
class AcrodeBrandingFilters
{
	public static $acTheme = 'acrode';
	public static $acThemeIcon = '/wp-content/themes/acrode-divi-starter/img/acrode.svg';
	public static $acThemeShowFrontendIcon = true;
	public static $acThemeFrontendIcon = '/wp-content/themes/acrode-divi-starter/img/acrode.svg';

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
				global $menu, $submenu;

				foreach ($menu as $key => $item) {
					if ($item[0] === 'Divi') {
						$menu[$key][0] = self::$acTheme;
					}
				}
				// Remove Divi Support Page
				remove_submenu_page('et_divi_options', 'et_support_center_divi');
				remove_submenu_page('rocket_manage_options', 'options-general.php?page=wprocket#dashboard');
				//var_dump($submenu);
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

			public static function custom_login_logo()
			{
				echo '<style type ="text/css">.login h1 a { display:none!important; }</style>';
			}

			/*
			public static function onThemeSetUp()
			{
				global $themename;
				$themename = self::$acTheme;
			}
			*/

			public static function kill_divi($themes)
			{
				unset($themes['Divi']);
				return $themes;
			}

			public static function frontend_backlink()
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
			<a id="acrode-wpauthor" href="https://acrode.com" target="_blank" title="WordPress Theme Author"><img width="21px" src="<?php echo self::$acThemeFrontendIcon ?>"></a>
	<?php
		}

		public static function setup()
		{
			// TODO: Fix language Bug on name change
			//add_action('after_setup_theme', array('AcrodeBrandingFilters', 'onThemeSetUp'), 9999);

			add_action('admin_bar_menu', array('AcrodeBrandingFilters', 'filterAdminBarMenu'), 9999);
			add_action('et_fb_enqueue_assets', array('AcrodeBrandingFilters', 'fbAssets'), 9);

			add_filter('option_et_bfb_settings', array('AcrodeBrandingFilters', 'filterWpOption'), 10, 2);
			add_filter('et_fb_help_videos', array('AcrodeBrandingFilters', 'filterBuilderHelpVideos'));

			if (is_admin()) {
				add_action('admin_menu', array('AcrodeBrandingFilters', 'modifyAdminMenu'), 9999);
				add_action('admin_head', array('AcrodeBrandingFilters', 'adminCssJs'));

				add_action('et_pb_before_page_builder', array('AcrodeBrandingFilters', 'builderScript'));

				add_filter('display_post_states', array('AcrodeBrandingFilters', 'filterPostStates'), 9999);
				//add_filter('et_builder_settings_definitions', array('AcrodeBrandingFilters', 'filterThemeOptionsDefinitionsBuilder'));
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

			// Frontend Branding
			if (self::$acThemeShowFrontendIcon) {
				add_action('wp_footer', array('AcrodeBrandingFilters', 'frontend_backlink'));
			}

			// White label Login
			add_action('login_head', array('AcrodeBrandingFilters', 'custom_login_logo'));

			// Kill Divi theme
			add_filter('wp_prepare_themes_for_js', array('AcrodeBrandingFilters', 'kill_divi'));
		}
	}

	AcrodeBrandingFilters::setup();
