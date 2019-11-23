<?php
if (file_exists(ABSPATH . '/acrode/theme/single.php')) {
	require ABSPATH . '/acrode/theme/single.php';
} else {
    require ABSPATH . '/wp-content/themes/Divi/single.php';
}