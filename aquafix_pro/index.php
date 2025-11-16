<?php
defined('ENVIRONMENT') || define('ENVIRONMENT', 'development');

switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;
    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

$system_path = 'system';
$application_folder = 'application';
$view_folder = '';

if (realpath($system_path) !== FALSE) {
    $system_path = realpath($system_path).DIRECTORY_SEPARATOR;
}

$system_path = rtrim($system_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

if (!is_dir($system_path)) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3);
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_path);
define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('SYSDIR', basename(BASEPATH));

if (is_dir($application_folder)) {
    if (realpath($application_folder) !== FALSE) {
        $application_folder = realpath($application_folder);
    }
    $application_folder = rtrim($application_folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
} elseif (is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR)) {
    $application_folder = BASEPATH.rtrim($application_folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
} else {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
    exit(3);
}

define('APPPATH', $application_folder);

if (!isset($view_folder[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR)) {
    $view_folder = APPPATH.'views'.DIRECTORY_SEPARATOR;
} elseif (is_dir($view_folder)) {
    if (realpath($view_folder) !== FALSE) {
        $view_folder = realpath($view_folder);
    }
    $view_folder = rtrim($view_folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
} elseif (is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR)) {
    $view_folder = APPPATH.rtrim($view_folder, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
} else {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
    exit(3);
}

define('VIEWPATH', $view_folder);

require_once BASEPATH.'core/CodeIgniter.php';
