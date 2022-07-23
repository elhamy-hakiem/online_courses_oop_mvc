<?php
require("../globals.php");
require(MODELS."/coursesStudentsModel.php");
require(MODELS."/coursesModel.php");
require(MODELS."/usersModel.php");
require(INSTRUCTORCONTROLLER."/instructorStudentsController.php");

$instructorStudentsController = new instructorStudentsController();
// Start define action 
$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage';
// End define action

// Start Mangae Students
if($action == 'manage')
{
    $instructorStudentsController->getStudents();
}
// End Mangae Students

// Start Approve Students
elseif($action == 'approve')
{
    $instructorStudentsController->approveStudents();
}
// End Approve Students

// Start View Student Details
elseif($action == 'view')
{
    $instructorStudentsController->viewStudent();
}
// End View Student Details

// Start Delete Students
elseif($action == 'delete')
{
    $instructorStudentsController->deleteStudentFromCourse();
}
// End Delete Students

else
{
    $instructorStudentsController->PageNotFound();
}