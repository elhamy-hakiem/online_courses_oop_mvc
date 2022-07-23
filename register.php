<?php
    require("globals.php");
    require(MODELS."/usersModel.php");
    require(CONTROLLERS."/usersController.php");

    $userController = new usersController();

    $userController ->userRegister();

?>