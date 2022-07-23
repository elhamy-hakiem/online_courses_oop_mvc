<?php
require("../globals.php");
require(MODELS."/coursesLessonsModel.php");
require(MODELS."/coursesModel.php");
require(MODELS."/coursesLessonsCommentsModel.php");
require(MODELS."/usersModel.php");
require(INSTRUCTORCONTROLLER."/instructorCommentsController.php");
require(INSTRUCTORCONTROLLER."/instructorLessonsController.php");

$instructorLessonsController = new instructorLessonsController();

$instructorCommentsController = new instructorCommentsController();
// Start define action 
$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage';
// End define action
$courseId     = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) :0;

// Start Add Lessons To Course
if($action == 'add')
{
    $instructorCommentsController->checkInstructorHaveCourse($courseId);
    $instructorCommentsController->addComments();
    $instructorLessonsController->viewLessonDetails();
}
elseif($action == 'delete')
{
    $instructorCommentsController->checkInstructorHaveCourse($courseId);
    $instructorCommentsController->deleteComments();
    $instructorLessonsController->viewLessonDetails();
}

else
{
    $instructorCommentsController->PageNotFound();
}