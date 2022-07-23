<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class usersController extends controller
{
    private $usersModel ;
    public function __construct()
    {
        $this->usersModel = new usersModel();
    }

    /**
     * Start User Register
     */
    public function userRegister()
    {
        if(checkLogin())
        {
            invalidRedirect();
        }
        if(isset($_POST['register']))
        {
            $filterUsername   = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $filterPassword   = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
            $filterEmail      = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $filterGroup      = filter_var($_POST['usergroup'],FILTER_SANITIZE_NUMBER_INT);

            // Check If Username Already Exist 
            $checkUsername = $this->usersModel->getUsers("WHERE `username` = '$filterUsername' LIMIT 1");

            if(count($checkUsername) > 0)
            {
                $this->setControllerErrors("Username Already Exist");
            }
            elseif(strlen($filterUsername) < 4)
            {
                $this->setControllerErrors("Username Must Be At Least 4 chars");
            }
            elseif(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $filterPassword))
            {
                $this->setControllerErrors("Password Must Be have at least one number and at least one letter and there have to be 8-12 characters");
            }
            elseif(! filter_var($filterEmail,FILTER_VALIDATE_EMAIL))
            {
                $this->setControllerErrors("Please Type Validate Email Adress");
            }
            elseif($filterGroup != 2  && $filterGroup != 3)
            {
                $this->setControllerErrors("You Must Choose User Group");
            }
            else
            {                        
                $userData = array(
                    'username'   => $filterUsername,
                    'password'   => hashPasswords($filterPassword),
                    'email'      => $filterEmail,
                    'user_group' => $filterGroup
                );
                if($this->usersModel->addUser($userData))
                    $this->setControllerSuccessMsg("Register Success");
                else
                    $this->setControllerErrors($this->usersModel->getError());
            }
        }

        include(VIEWS."/front/register.html");

    }

    /**
     * Start User Login And Forget Password
     */
    public function usersLogin()
    {
        if(checkLogin())
        {
            invalidRedirect();
        }
        // Start Login 
        if(isset($_POST['login']))
        {
            $username = $_POST['username'];
            $pasword  = $_POST['password'];

            if(strlen($username) < 3)
            {
                $this->setControllerErrors("Username Must Greater Than 3 Chars");
            }
            if(strlen($pasword) < 3)
            {
                $this->setControllerErrors("password Must Greater Than 3 Chars");
            }
            
            if($this->usersModel->login($username,$pasword))
            {
                $userData = $this->usersModel->getUserData();
                $_SESSION['user'] =  $userData;

                invalidRedirect();                
            }
            else
            {
                $this->setControllerErrors($this->usersModel->getError());
                // page view 2 numbers
               // include(VIEWS."/front/login.html");
            }
        }
        // End Login 
        // Start Forget Password 
        elseif($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resetPass']))
        {
            $email = filter_var($_POST['email'] , FILTER_SANITIZE_EMAIL);

            if(empty($email))
            {
                $this->setControllerErrors("Sorry Email Can't Be <strong>Empty</strong>");
            }
            else
            {
                if(filter_var($email,FILTER_VALIDATE_EMAIL) != true )
                {
                    $this->setControllerErrors('This Email Is Not <strong>Valid</strong>');
                }
                else
                {
                    //Check if email is Exist Or not
                    $check_Email = $this->usersModel->getUsers("WHERE `email` = '$email' LIMIT 1");
              
                    if(count($check_Email) == 1)
                    {
                        $user = $check_Email[0];
                        if($user['user_group'] != 2 && $user['user_group'] != 3)
                        {
                            $this->setControllerErrors("This User is Not <strong> Exist </strong>");
                        }
                        else
                        {
                            $resetPasswordModel = new resetPasswordModel();
                            // Generate Code To Use For Reset Password 
                            $code =bin2hex(random_bytes(22));
                            $userData = array(
                                'code'     => $code,
                                'email'    => $email
                            );
                            $resetPasswordModel->addCode($userData);

                            // Start Send Mail To Reset Password 
                            $mail = new PHPMailer();
                            try 
                            {                
                                //Server settings
                                $mail->isSMTP();                                            // Send using SMTP
                                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication

                                $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
                                $mail->Username   = 'onlineshopservices6@gmail.com';       // SMTP username
                                $mail->Password   = 'xclcikgpgoumdjzp';                    // SMTP password
                                $mail->SMTPSecure = 'tls';       // Enable Tls encryption;
                                $mail->Port       = 587;                                  // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                                //Recipients
                                $mail->setFrom('onlineshopservices6@gmail.com', 'MOOC SYSTEM');
                                $mail->addAddress($email, $user['username']);             // Add a recipient
                                $mail->addReplyTo('onlineshopservices6@gmail.com', 'MOOC SYSTEM');

                                // Content
                                $url = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/resetPassword.php?code=$code";
                                $mail->isHTML(true);                                  // Set email format to HTML
                                $mail->Subject = 'Reset Password - MOOC SYSTEM';
                                $mail->Body    = '
                                    <div style="text-align:center; background-color:#41cac0; padding:20px;">
                                        <p style="color:#fff; font-size:15px;">
                                            Hello '.$user['username'].' Now You Can Reset Password From Here 
                                        </p>
                                        <a style="background-color: #f67a6e;color:#fff;padding:10px; font-weight:bold; border-radius:10px;box-shadow: 0 4px #e56b60;" href="'.$url.'">Reset Password</a>
                                    </div>
                                ';

                                $mail->send();
                               $this->setControllerSuccessMsg("Check Your Email To Change Password ");
                            } 
                            catch (Exception $e) 
                            {
                                $this->setControllerErrors("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                            }
                        }
                    }
                    else
                    {
                         $this->setControllerErrors(" This Email is Not <strong> Exist </strong>");
                    }
                }
            }
        }
        // View Login Page 
        include(VIEWS."/front/login.html");
    }
    /**
     * Start User Change Password
     */
    public function changePassword()
    {
        if(checkLogin())
        {
            invalidRedirect();
        }

        if(isset($_GET['code']))
        {
            $resetPasswordModel = new resetPasswordModel();
            $code = $_GET['code'];
            $user = $resetPasswordModel->getUserByCode($code);
            if(empty($user))
            {
                $this->usersLogin();
                exit;
            }
            else
            {
                if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changePassword']))
                {
                    $password            = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
                    $confirmPassword     = filter_var($_POST['confirmPass'],FILTER_SANITIZE_STRING);
                    $resetPasswordErrors = 0;

                    if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password))
                    {
                        $this->setControllerErrors("Password Must Be have at least one number and at least one letter and there have to be 8-12 characters");
                        $resetPasswordErrors +=1;
                    }
                    if(! password_verify($confirmPassword,hashPasswords($password)))
                    {
                        $this->setControllerErrors("Password And Confirm Password  Is Not  <strong>Match</strong>");
                        $resetPasswordErrors +=1;
                    }
                    if($resetPasswordErrors == 0)
                    {
                        $data = array(
                            'password'  =>hashPasswords($password)
                        );
                        if($this->usersModel->changePassword($user['email'],$data))
                        {
                            if($resetPasswordModel->deleteCode($code))
                                $this->setControllerSuccessMsg("Password Changed ");
                            else
                                $this->setControllerErrors("Code Not Deletd");
                        }
                        else
                        {
                            $this->setControllerErrors($this->usersModel->getError());
                        }
                    } 
                    
        
                }
            }
        }
        else
        {
            $this->usersLogin();
            exit;
        }

        // View Login Page 
        include(VIEWS."/front/resetPassword.html");   
    }
}