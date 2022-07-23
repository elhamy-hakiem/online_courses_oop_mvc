<?php
require("../globals.php");
require(MODELS."/coursesModel.php");
require(MODELS."/coursesCategoriesModel.php");
require(MODELS."/coursesLessonsModel.php");
require(MODELS."/coursesStudentsModel.php");
require(INSTRUCTORCONTROLLER."/instructorCoursesController.php");

$instructorCourseController = new instructorCoursesController();

// Start define action 
$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage';
// End define action 

// start Manage courses 
if($action == 'manage')
{
    $instructorCourseController->getInstructorCourses();
}
// End Manage courses 

// start Add courses 
elseif($action == 'add')
{
    $instructorCourseController->addCourse();
}
// End Add courses 

// start View course
elseif($action == 'view')
{
    $instructorCourseController->viewCourse();
}
// End View course 

// start Update courses 
elseif($action == 'update')
{
    $instructorCourseController->updateCourse();
}
// End Update courses 

// start Delete courses 
elseif($action == 'delete')
{
    $instructorCourseController->deleteCourse();
}
// End Delete courses 

else
{
    $instructorCourseController->PageNotFound();
}