<?php
/**
 * Main application entry point
 */

// Start session
session_start();

// Require configuration
require_once '../app/config/config.php';

// Require Composer Autoloader
require_once '../vendor/autoload.php';

// Require core files
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';
require_once '../app/core/Model.php';

// Load helpers
require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/MailHelper.php';
require_once '../app/helpers/image_helper.php';
require_once '../app/helpers/nav_helper.php';
require_once '../app/helpers/content_helper.php';

// Init Core App
$app = new App();
