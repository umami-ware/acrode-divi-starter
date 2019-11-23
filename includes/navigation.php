<?php
if (file_exists(ABSPATH . 'acrode/theme/includes/navigation.php')) {
	require ABSPATH . 'acrode/theme/includes/navigation.php';
} else {
    require ABSPATH . 'wp-content/themes/Divi/includes/navigation.php';
}