<?php
require('../globals.php');
require(MODELS."/usersModel.php");
require(MODELS."/coursesModel.php");
require(MODELS."/coursesCategoriesModel.php");
require(MODELS."/coursesLessonsModel.php");

require(ADMINCONTROLLER.'/adminController.php');

$adminController =new adminController();

$adminController->index();

