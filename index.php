<?php
if (file_exists(ABSPATH . 'acrode/theme/index.php')) {
	require ABSPATH . 'acrode/theme/index.php';
} else {
    require ABSPATH . 'wp-content/themes/Divi/index.php';
}