<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Load Composer's autoloader
    require ("includes/lib/vendor/autoload.php");
    
    require("globals.php");
    require(MODELS."/usersModel.php");
    require(MODELS."/resetPasswordsModel.php");
    require(CONTROLLERS."/usersController.php");

    $userController = new usersController();

    $userController ->usersLogin();

?>