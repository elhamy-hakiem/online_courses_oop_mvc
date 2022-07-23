<?php
require("../globals.php");
require(MODELS."/coursesModel.php");
require(MODELS."/coursesLessonsModel.php");
require(MODELS."/usersModel.php");
require(INSTRUCTORCONTROLLER."/instructorController.php");

$instructorController = new instructorController();

// Start define action 
$action = isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : '';
// End define action 

if($action == 'profile')
{
    $instructorController->showProfile();
}
else
{
    $instructorController->index();
}

