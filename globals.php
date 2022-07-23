<?php
session_start();
// Define Directory paths 
define('ROOT',dirname(__FILE__));
define('INCLUDES',ROOT.'/includes');
define('UPLOADS',ROOT.'/uploads');
define('PLUGINS',INCLUDES.'/plugins');

define('CONTROLLERS',INCLUDES.'/controllers');
define('ADMINCONTROLLER',CONTROLLERS.'/admin');
define('INSTRUCTORCONTROLLER',CONTROLLERS.'/instructor');
define('STUDENTCONTROLLER',CONTROLLERS.'/student');

define('MODELS',INCLUDES.'/models');
define('VIEWS',ROOT.'/templates');
define('ASSETS',ROOT.'/assets');

// Include Config File 
require(INCLUDES.'/config.php');

require(INCLUDES.'/functions/general.functions.php');

require(INCLUDES.'/classes/Redirect.php');
require(INCLUDES.'/classes/system.php');
require(INCLUDES.'/classes/mysql.php');
require(INCLUDES.'/classes/uploadImage.php');

require(CONTROLLERS.'/controller.php');
require(MODELS.'/model.php');

// Store New Object From DataBase Class 
system::Set('db',new mysqlDB());
