<?php

class controller
{
    public function __construct()
    {
        ob_start();
        // check user login 
        if(! checkLogin())
            Redirect::TO('../login.php');
            
        $this->clearsuccessMsg();
        $this->clearErrors();
    }
    // Check User Group 
    public function checkPermission($groupId)
    {
        if(isset($_SESSION['user']['user_group']) && $_SESSION['user']['user_group'] != $groupId )
        {
            // Admin
            if($_SESSION['user']['user_group'] == 1)
            {
                header('LOCATION:../admin');
            }
            // instructor
            if($_SESSION['user']['user_group'] == 2)
            {
                header('LOCATION:../instructor');
            }
            // student 
            if($_SESSION['user']['user_group'] == 3)
            {
                header('LOCATION:../student');
            }
            exit;
        }
        else
        {
            if(!isset($_SESSION['user']['user_group']))
            {
                header('LOCATION:login.php');
                exit;
            }
        }
    }

    // Set Errors Message 
    public function setControllerErrors($errors)
    {
        if(is_array($errors))
        {
            foreach($errors as $error)
            {
                $_SESSION['errors'][] = $error;
            }
        }
        else
        {
            $_SESSION['errors'][] = $errors;
        }

    }
    // clear Errors Message
    public function clearErrors()
    {
        unset($_SESSION['errors']);
    }
    

    // Set Success Message 
    public function setControllerSuccessMsg($Msg)
    {
        $_SESSION['successMsg'] = $Msg;
    }

    
    // clear Success Message 
    public function clearsuccessMsg()
    {
        unset($_SESSION['successMsg']);
    }

    /**
     * View Error 404 page
     */
    public function PageNotFound()
    {
       include(VIEWS."/back/admin/404.html");
    }
    
}