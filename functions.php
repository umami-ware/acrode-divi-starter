<?php
/************************************/
/**** Optional: Development Mode ****/
/************************************/
// SETUP BEGIN
$GLOBALS['acViewSiteKey'] = 'gabriel';
$GLOBALS['acReleaseTag'] = 1;
$GLOBALS['acReleaseMonat'] = 9;
$GLOBALS['acReleaseJahr'] = 2019;
// SETUP END
///
function ac_development_design() {
	$endTime = mktime(0, 0, 0, $GLOBALS['acReleaseMonat'], $GLOBALS['acReleaseTag'], $GLOBALS['acReleaseJahr']); //Stunde, Minute, Sekunde, Monat, Tag, Jahr; 
	//Aktuellezeit des microtimestamps nach PHP5, für PHP4 muss eine andere Form genutzt werden. 
	$timeNow = microtime(true);
	//Berechnet differenz von der Endzeit vom jetzigen Zeitpunkt aus. 
	$diffTime = $endTime - $timeNow;
	//Zerlegt $diffTime am Dezimalpunkt, rundet vorher auf 2 Stellen nach dem Dezimalpunkt und gibt diese zurück.
	$milli = explode(".", round($diffTime, 2));
	$millisec = round($milli[1]); 
	//Berechnung für Tage, Stunden, Minuten 
	$day = floor($diffTime / (24*3600));
	$diffTime = $diffTime % (24*3600);
	$hours = floor($diffTime / (60*60));
	$diffTime = $diffTime % (60*60);
	$mins = floor($diffTime / 60);
	$secs = $diffTime % 60;
?>
	<style>
		html {
			background: linear-gradient(-180deg, #ac32e4, #7918f2, #4801ff) no-repeat !important;
		}
		body {
			background: transparent !important;
			box-shadow: none !important;
		}
		h1 {
			font-size: 51px !important;
			line-height: 1.1;
			color: rgba(255,255,255,0.75) !important;
			text-transform: uppercase;
			letter-spacing: 10px;
			text-align: center;
		}
		h2 {
			text-align: center;
			color: #fff !important;
			margin-bottom: 50px;
		}
		.ac-development-container {
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}
		.ac-countdown-container {
			display: flex;
			align-items: center;
    		justify-content: center;
		}
		.ac-countdown {
			display: flex;
    		flex-direction: column;
			color: #fff;
			margin: 0 25px;
		}
		.ac-countdown > span:first-child {
			font-size: 85px;
		}
		.ac-unit {
			text-align: center;
			font-size: 17px;
			font-weight: bold;
			text-transform: uppercase;
			color: rgba(255,255,255,0.85);
		}
		@media (max-width: 620px) {
			h1 {
				font-size: 31px !important;
			}
			h2 {
				font-size: 22px !important;
			}
			.ac-countdown {
				margin: 0 10px;
			}
			.ac-countdown > span:first-child {
				font-size: 11vw;
			}
			.ac-unit {
				font-size: 11px;
			}
		}
		@media (max-height: 299px), (max-width: 299px) {
			body  {
				display: none !important;
			}
			html::after {
				content: 'Leider ist die Bildschirmgröße zu klein. Bitte besuchen Sie die Seite auf einem Gerät mit einem größerem Bildschirm.';
				display: block !important;
				margin: auto;
				font-size: 26px;
				line-height: 26px;
				color: #fff;
				text-align: center;
				padding: 0 10px;
				word-break: break-word;
			}
		}
	</style>
	<div class="ac-development-container">
		<h1>COMING SOON!</h1><h2>Die Webseite ist aktuell noch in der Entwicklung</h2>
		<div class="ac-countdown-container">
			<div class="ac-countdown">
				<span id="ac-days"><?php echo $day; ?></span>
				<span class="ac-unit">Tage</span>
			</div>
			<div class="ac-countdown">
				<span id="ac-hours"><?php echo $hours; ?></span>
				<span class="ac-unit">Stunden</span>
			</div>
			<div class="ac-countdown">
				<span id="ac-minutes"><?php echo $mins; ?></span>
				<span class="ac-unit">Minuten</span>
			</div>
			<div class="ac-countdown">
				<span id="ac-seconds"><?php echo $secs; ?></span>
				<span class="ac-unit">Sekunden</span>
			</div>
		</div>
	</div>
	<script>
		var countDownDate = new Date(<?php echo $endTime; ?>*1000).getTime();
			elDays = document.getElementById("ac-days"),
			elHours = document.getElementById("ac-hours"),
			elMinutes = document.getElementById("ac-minutes"),
			elSeconds = document.getElementById("ac-seconds");
		var x = setInterval(function() {
			// Get today's date and time
			var now = new Date().getTime();
			// Find the distance between now and the count down date
			var distance = countDownDate - now;
			
			elDays.innerHTML = Math.floor(distance / (1000 * 60 * 60 * 24));
			elHours.innerHTML = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			elMinutes.innerHTML = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			elSeconds.innerHTML = Math.floor((distance % (1000 * 60)) / 1000);
		}, 1000);
	</script>
<?php
}
function ac_link_script() {
?>
	<script>
		document.querySelectorAll('a').forEach(function(elem) {
			elem.addEventListener('click', function(e) {
				var href = e.target.href,
					location = window.location;
				if (location.href.split('#').shift() !== href.split('#').shift()) {
					e.preventDefault();
					var splitHash = href.split('#'),
						getParams = location.search.substr(1),
						newLink;
					if (splitHash[0].indexOf('?') === -1) {
						newLink = splitHash.shift() + '?' + getParams;
					}
					else {
						newLink = splitHash.shift() + '&' + getParams;
					}
					if (splitHash.length) {
						newLink += '#' + splitHash.join('');
					}
					window.location.href = newLink;
				}
			});
		});
	</script>
<?php
}
function ac_development_mode() {
	$showSite = $_GET['viewSiteKey'];
    if($showSite !== $GLOBALS['acViewSiteKey'] && (!current_user_can('edit_themes') || !is_user_logged_in())) {
        wp_die(ac_development_design());
    } else {
		add_action('wp_footer', 'ac_link_script');
	}
}
add_action('get_header', 'ac_development_mode');

/***************************************************/
/**** Optional: Add global Divi module anywhere ****/
/***************************************************/
function acrode_show_gdivi_module_shortcode($gdId) {
	extract(shortcode_atts(array('id' =>'*'), $gdId)); 
	return do_shortcode('[et_pb_section global_module="'.$id.'"][/et_pb_section]');
}
add_shortcode('acrode_gdivi_module', 'acrode_show_gdivi_module_shortcode');

/************************/
/**** Necessary Code ****/
/************************/
function acrode_enqueue() {
    wp_enqueue_style('parent-style', get_template_directory_uri().'/style.css');
	wp_enqueue_script('custom-scripts', get_stylesheet_directory_uri().'/script.js', array('jquery'));
}
/* acrode remove type from style tags */
function acrode_codeless_remove_type_attr($tag) {
    return preg_replace('/type=[\'"]text\/(css|javascript)[\'"]/', '', $tag);
}
/* acrode get html sitemap */
function acrode_get_html_sitemap($atts) {
	$return = '';
	$args = array('public' => 1);
	$ignoreposttypes = array('attachment', 'project');
	$post_types = get_post_types($args, 'objects');
	foreach ($post_types as $post_type) {
		if( !in_array($post_type->name, $ignoreposttypes)) {
			$return .= '<h2>'.$post_type->labels->name.'</h2>';
			$args = array(
				'posts_per_page' => -1,
				'post_type' => $post_type->name,
				'post_status' => 'publish'
			);
			$posts_array = get_posts($args); 
			$return .=  '<ul>';
			foreach($posts_array as $pst) {
				$return .=  '<li><a title="' . $pst->post_title.'" href="'.get_permalink($pst->ID).'">'.$pst->post_title.'</a></li>';
			}
			$return .=  '</ul>';
		}
	}
	return $return;
}
/* acrode remove hEntry */
function acrode_remove_hentry_class($classes) {
	$classes = array_diff($classes, array('hentry'));
	return $classes;
}
add_action('wp_enqueue_scripts', 'acrode_enqueue');
/* acrode set filter for style and script tags */
add_filter('style_loader_tag', 'acrode_codeless_remove_type_attr', 10, 2);
add_filter('script_loader_tag', 'acrode_codeless_remove_type_attr', 10, 2);
/* acrode add html sitemap shortcode */
add_shortcode('acrode_html_sitemap', 'acrode_get_html_sitemap');
/* acrode remove hEntry filter */
add_filter('post_class', 'acrode_remove_hentry_class');

/* acrode remove pages from search */
if (!is_admin()) {
	function wpb_search_filter($query) {
		if ($query->is_search) {
			$query->set('post_type', 'post');
		}
		return $query;
	}
	add_filter('pre_get_posts','wpb_search_filter');
}
