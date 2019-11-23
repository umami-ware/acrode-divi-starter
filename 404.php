<?php
if (file_exists(ABSPATH . '/acrode/theme/404.php')) {
	require ABSPATH . '/acrode/theme/404.php';
} else {
    require ABSPATH . '/wp-content/themes/Divi/404.php';
}