<?php
require("../globals.php");
require(MODELS."/coursesModel.php");
require(MODELS."/coursesLessonsModel.php");
require(ADMINCONTROLLER."/adminCourseLessonsController.php");

// course lessons Controller
$adminCourseLessonsController = new adminCourseLessonsController();

// Start define action 
$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage';
// End define action 


// start View course Lessons 
if($action == 'manage')
{
    $adminCourseLessonsController ->getCourseLessons();
}
// End View course Lessons 

// start Delete course Lessons 
elseif($action == 'delete')
{
    $adminCourseLessonsController ->deletelesson();
}
// End Delete course Lessons 

else
{
    $adminCourseLessonsController -> PageNotFound();
}