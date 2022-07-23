<?php
require("../globals.php");
require(MODELS."/coursesModel.php");
require(ADMINCONTROLLER."/adminCoursesController.php");

// Start page 
$adminCoursesController = new adminCoursesController();


// Start define action 
$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage';
// End define action 

// start Manage courses 
if($action == 'manage')
{
    $adminCoursesController ->getCourses();
}
// End Manage courses 

// start search courses 
elseif($action == 'search')
{
    $adminCoursesController ->searchCourses();
}
// End Search courses 

// start Delete courses 
elseif($action == 'delete')
{
    $adminCoursesController ->deleteCourses();
}
// End Delete courses 

else
{
    $adminCoursesController -> PageNotFound();
}