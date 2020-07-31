<?php
require_once APPPATH . '/config/common.config.php'; // Common configuration
require_once APPPATH . '/config/db.config.php'; // Database configuration
require_once APPPATH . '/config/i18n.config.php'; // i18n configuration

// ASCII Secure random crypto key
define("CRYPTO_KEY", "def000009cf556614b8b802e2b9ffbde2944f5c5ab6b5b59b3fda4457a42075e23d53f01dc4cdf274367031c45d18d7d42ba38666db62828b86b76f3b54a71a134f36c6a");

// General purpose salt
define("NP_SALT", "XOg6Lxx624WR0HNX");


// Path to instagram sessions directory
define("SESSIONS_PATH", APPPATH . "/sessions");
// Path to temporary files directory
define("TEMP_PATH", ROOTPATH . "/assets/uploads/temp");


// Path to themes directory
define("THEMES_PATH", ROOTPATH . "/inc/themes");
// URI of themes directory
define("THEMES_URL", APPURL . "/inc/themes");


// Path to plugins directory
define("PLUGINS_PATH", ROOTPATH . "/inc/plugins");
// URI of plugins directory
define("PLUGINS_URL", APPURL . "/inc/plugins");

// Path to ffmpeg binary executable
// NULL means it's been installed on global path
// If you set the value other than null, then it will only be 
// validated during posting the videos
define("FFMPEGBIN", NULL);

// Path to ffprobe binary executable
// NULL means it's been installed on global path
// If you set the value other than null, then it will only be 
// validated during posting the videos
define("FFPROBEBIN", NULL);
