<?php
require("globals.php");
require(MODELS."/usersModel.php");
require(MODELS."/resetPasswordsModel.php");
require(CONTROLLERS."/usersController.php");

$userController = new usersController();

$userController ->changePassword();