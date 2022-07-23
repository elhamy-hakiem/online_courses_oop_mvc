<?php

class adminUsersController extends controller
{
    private $usersModel;
    private $usersGroupsModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(1);
        $this->usersModel = new usersModel();
        $this->usersGroupsModel = new usersGroupsModel();
    }

    

    /**
     * Start get all Users
     */
    public function getUsers()
    {
        $groupId = isset($_GET['gid']) && is_numeric($_GET['gid']) ? intval($_GET['gid']) : 0;

        $groupData = $this->usersGroupsModel->getUserGroupById($groupId);
        
        if(count($groupData) > 0)
            $users = $this->usersModel->getUsersByGroup($groupId);
        else
            // model -> get all Users
            $users = $this->usersModel ->getUsers(); 

        // View  -> display Users 
        include(VIEWS."/back/admin/header.html");
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/users.html");
        include(VIEWS."/back/admin/footer.html");
    }

   /**
     * Start Add New User
    */
    public function addUser()
    {
        $groups = $this->usersGroupsModel->getUsersGroups();
        if(isset($_POST['adduser']))
        {
            $filterUsername   = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $filterPassword   = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
            $filterEmail      = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $filterGroup      = filter_var($_POST['usergroup'],FILTER_SANITIZE_NUMBER_INT);

            $imageData        = $_FILES['userImage'];
            $uploadImage      = new uploadImage($imageData);

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
            elseif($filterGroup == 0  || empty($filterGroup))
            {
                $this->setControllerErrors("You Must Choose User Group");
            }
            else
            {
                $uploadImage->validateImage();
                if(count($uploadImage->getImageErrors()) > 0)
                {
                   $this->setControllerErrors($uploadImage->getImageErrors());
                }
                else
                {
                    if($uploadImage->uploadFile(UPLOADS."/users"))
                    {
                        $newImageName = $uploadImage->newImageName;
                        
                        $userData = array(
                            'username'   => $filterUsername,
                            'password'   => hashPasswords($filterPassword),
                            'email'      => $filterEmail,
                            'image'      => $newImageName,
                            'user_group' => $filterGroup
                        );
                        if($this->usersModel->addUser($userData))
                        {
                            $this->setControllerSuccessMsg("User Added");
                        }
                        else
                        {
                            $this->setControllerErrors($this->usersModel->getError());
                        }
                    }
                    else
                    {
                       $this->setControllerErrors($uploadImage->getImageErrors());
                    }
                }
            }
        }

        // View Page Header
        include(VIEWS."/back/admin/header.html");
        // View Page Content 
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/adduser.html");
        include(VIEWS."/back/admin/footer.html");
    }

   /**
     * Start Update User
    */
    public function updateUser()
    {
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        $groups = $this->usersGroupsModel->getUsersGroups();
        $userData = $this->usersModel->getUser($userid);

        if(empty($userData))
        {
            $this->PageNotFound();
            exit;
        }

        // Disabled Edit Admin Information 
        if($userData['user_group'] == 1)
        {
            $this->setControllerErrors("You Can't Update Admin");
            // view All User 
            $this->getUsers();
            exit;
        }

        if(isset($_POST['updateuser']))
        {
            // prepare Data 
            $filterUsername   = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $filterPassword   = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
            $filterEmail      = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $filterGroup      = filter_var($_POST['usergroup'],FILTER_SANITIZE_NUMBER_INT);

            $oldPassword      = $_POST['oldpassword'];
            $oldImage         = $_POST['oldImage'];
            $newImageName     ='';

            $imageData        = $_FILES['userImage'];
            $imageName        = $imageData['name'];
           

            $updateErrors     = 0;

            // Check If Username Already Exist 
            $checkUsername = $this->usersModel->getUsers("WHERE `username` = '$filterUsername' AND `user_id` != $userid LIMIT 1");

            if(count($checkUsername) > 0)
            {
                 $this->setControllerErrors("Username Already Exist");
                 $updateErrors += 1 ;
            }
            // Check username length
            if(strlen($filterUsername) < 4)
            {
                $this->setControllerErrors("Username Must Be At Least 4 chars");
                $updateErrors += 1 ;
            }
            // check Email Address
            if(! filter_var($filterEmail,FILTER_VALIDATE_EMAIL))
            {
                $this->setControllerErrors("Please Type Validate Email Address");
                $updateErrors += 1 ;
            }
            // check user Group 
            if($filterGroup == 0  || empty($filterGroup))
            {
                $this->setControllerErrors("You Must Choose User Group");
                $updateErrors += 1 ;
            }
            //Start password Validate
            if(!empty($filterPassword))
            {
                if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $filterPassword))
                {
                    $this->setControllerErrors("Password Must Be have at least one number and at least one letter and there have to be 8-12 characters");
                    $updateErrors += 1 ;
                }
                else
                {
                    $filterPassword = hashPasswords($filterPassword);
                }
            }
            if(empty($filterPassword))
            {
                if(empty($oldPassword))
                {
                    $this->setControllerErrors("Password Can't Be Empty ! ");
                    $updateErrors += 1 ;
                }
                $filterPassword = $oldPassword;
            }
            //Start Image Validate
            if(empty($imageName))
            {
                if(empty($oldImage))
                {
                    $this->setControllerErrors("Image Can't Be Empty ! ");
                    $updateErrors += 1 ;
                }
                $newImageName = $oldImage; 
            }
            if(!empty($imageName))
            {
                // new Object From Class Upload Image 
                $uploadImage      = new uploadImage($imageData);

                $uploadImage->validateImage();
                if(count($uploadImage->getImageErrors()) > 0)
                {
                   $this->setControllerErrors($uploadImage->getImageErrors());
                   $updateErrors += 1 ;
                }
                else
                {
                    if($uploadImage->uploadFile(UPLOADS."/users"))
                    {
                        $newImageName = $uploadImage->newImageName;
                    }
                    else
                    {
                       $this->setControllerErrors($uploadImage->getImageErrors());
                       $updateErrors += 1 ;
                    }
                }
            }

            // Update Users If No Errors
            if($updateErrors == 0)
            {       

                 $userData = array(
                    'username'   => $filterUsername,
                    'password'   => $filterPassword,
                    'email'      => $filterEmail,
                    'image'      => $newImageName,
                    'user_group' => $filterGroup
                );
                if($this->usersModel->updateUser($userid,$userData))
                {
                    $this->setControllerSuccessMsg("User Updated");
                }
                else
                {
                    $this->setControllerErrors($this->usersModel->getError());
                }
            }
        }

        // View Page Header
        include(VIEWS."/back/admin/header.html");
        // View Page Content 
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/updateuser.html");
        include(VIEWS."/back/admin/footer.html");
    }

     /**
     * Start Update Profile
    */
    public function showProfile()
    {
        $userid = $_SESSION['user']['user_id'];

        if(isset($_POST['updateprofile']))
        {
            // prepare Data 
            $filterUsername   = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $filterPassword   = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
            $filterEmail      = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);

            $oldPassword      = $_POST['oldpassword'];
            $oldImage         = $_POST['oldImage'];
            $newImageName     ='';

            $imageData        = $_FILES['userImage'];
            $imageName        = $imageData['name'];
           

            $updateErrors     = 0;

            // Check If Username Already Exist 
            $checkUsername = $this->usersModel->getUsers("WHERE `username` = '$filterUsername' AND `user_id` != $userid LIMIT 1");

            if(count($checkUsername) > 0)
            {
                 $this->setControllerErrors("Username Already Exist");
                 $updateErrors += 1 ;
            }
            // Check username length
            if(strlen($filterUsername) < 4)
            {
                $this->setControllerErrors("Username Must Be At Least 4 chars");
                $updateErrors += 1 ;
            }
            // check Email Address
            if(! filter_var($filterEmail,FILTER_VALIDATE_EMAIL))
            {
                $this->setControllerErrors("Please Type Validate Email Address");
                $updateErrors += 1 ;
            }
            //Start password Validate
            if(!empty($filterPassword))
            {
                if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $filterPassword))
                {
                    $this->setControllerErrors("Password Must Be have at least one number and at least one letter and there have to be 8-12 characters");
                    $updateErrors += 1 ;
                }
                else
                {
                    $filterPassword = hashPasswords($filterPassword);
                }
            }
            if(empty($filterPassword))
            {
                if(empty($oldPassword))
                {
                    $this->setControllerErrors("Password Can't Be Empty ! ");
                    $updateErrors += 1 ;
                }
                $filterPassword = $oldPassword;
            }
            //Start Image Validate
            if(empty($imageName))
            {
                if(empty($oldImage))
                {
                    $this->setControllerErrors("Image Can't Be Empty ! ");
                    $updateErrors += 1 ;
                }
                $newImageName = $oldImage; 
            }
            if(!empty($imageName))
            {
                // new Object From Class Upload Image 
                $uploadImage      = new uploadImage($imageData);

                $uploadImage->validateImage();
                if(count($uploadImage->getImageErrors()) > 0)
                {
                   $this->setControllerErrors($uploadImage->getImageErrors());
                   $updateErrors += 1 ;
                }
                else
                {
                    if($uploadImage->uploadFile(UPLOADS."/users"))
                    {
                        $newImageName = $uploadImage->newImageName;
                    }
                    else
                    {
                       $this->setControllerErrors($uploadImage->getImageErrors());
                       $updateErrors += 1 ;
                    }
                }
            }

            // Update Users If No Errors
            if($updateErrors == 0)
            {       

                 $userData = array(
                    'username'   => $filterUsername,
                    'password'   => $filterPassword,
                    'email'      => $filterEmail,
                    'image'      => $newImageName
                );
                if($this->usersModel->updateUser($userid,$userData))
                {
                    $this->setControllerSuccessMsg("User Updated");
                    $_SESSION['user'] = $this->usersModel->getUser($userid);
                }
                else
                {
                    $this->setControllerErrors($this->usersModel->getError());
                }
            }
        }

        // View Page Header
        include(VIEWS."/back/admin/header.html");
        // View Page Content 
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/profile.html");
        include(VIEWS."/back/admin/footer.html");
    }

    /**
     * Start Delete User
    */
    public function deleteUser()
    {
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        $userData = $this->usersModel->getUser($userid);

        if(empty($userData))
        {
            $this->PageNotFound();
            exit;
        }
        if($userData['user_group'] == 1)
        {
            $this->setControllerErrors("You Can't Delete Admin");
        }
        else
        {
            if($this->usersModel->deleteUser($userid))
                $this->setControllerSuccessMsg("User Deleted");
            else
                $this->setControllerErrors($this->usersModel->getError());
        }
        
        // view All User 
        $this->getUsers();
    }

    /**
     * Start Search Users
    */
    // public function searchUsers()
    // {
    //     $keyword = isset($_GET['q']) && is_string($_GET['q']) ? $_GET['q'] : 0;

    //     $users = $this->usersModel->searchUsers($keyword);
        
    //     // View  -> display Courses
    //     include(VIEWS."/back/admin/searchusers.html");
    // }
    
    public function __destruct()
    {
       ob_end_flush();
    }
}