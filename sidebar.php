<?php
if (file_exists(ABSPATH . '/acrode/theme/sidebar.php')) {
	require ABSPATH . '/acrode/theme/sidebar.php';
} else {
    require ABSPATH . '/wp-content/themes/Divi/sidebar.php';
}