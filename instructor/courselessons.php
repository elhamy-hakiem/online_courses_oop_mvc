<?php
require("../globals.php");
require(MODELS."/coursesLessonsModel.php");
require(MODELS."/coursesModel.php");
require(MODELS."/coursesLessonsCommentsModel.php");
require(INSTRUCTORCONTROLLER."/instructorLessonsController.php");

$instructorLessonsController = new instructorLessonsController();
// Start define action 
$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage';
// End define action

// Start Mangae Lessons
if($action == 'manage')
{
    $instructorLessonsController->getCourseLessons();
}
// End Mangae Lessons

// Start Add Lessons To Course
elseif($action == 'add')
{
    $instructorLessonsController->addCourseLessons();
}
// End Add Lessons To Course

// Start Update Lessons Course
elseif($action == 'update')
{
    $instructorLessonsController->updateCourseLessons();
}
// End Update Lessons Course

// Start View Lesson Details
elseif($action == 'view')
{
    $instructorLessonsController->viewLessonDetails();
}
// End View Lesson Details

// Start Delete Lessons From Course
elseif($action == 'delete')
{
    $instructorLessonsController->deleteCourseLessons();
}
// End Delete Lessons From Course

else
{
    $instructorLessonsController->PageNotFound();
}