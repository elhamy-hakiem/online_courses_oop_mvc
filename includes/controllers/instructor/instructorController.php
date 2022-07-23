<?php

class instructorController extends controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(2);
    }
    public function index()
    {
        $instructorId = $_SESSION['user']['user_id'];
        $coursesModel    = new coursesModel();
        $lessonsModel    = new coursesLessonsModel();

        // Total Courses 
        $numCourses = count($coursesModel->getCourseByInstructorId($instructorId));

        // Total Lessons 
        $numLessons = count($lessonsModel->getLessonsByInstructorId($instructorId));

        include(VIEWS."/back/instructor/header.html");

        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/index.html");

        include(VIEWS."/back/instructor/footer.html");
    }

    /**
     * Start Update Profile
    */
    public function showProfile()
    {
        $userid = $_SESSION['user']['user_id'];
        // new object from userModel 
        $usersModel = new usersModel();

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
            $checkUsername =  $usersModel->getUsers("WHERE `username` = '$filterUsername' AND `user_id` != $userid LIMIT 1");

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
                if( $usersModel->updateUser($userid,$userData))
                {
                    $this->setControllerSuccessMsg("User Updated");
                    $_SESSION['user'] =  $usersModel->getUser($userid);
                }
                else
                {
                    $this->setControllerErrors( $usersModel->getError());
                }
            }
        }

        // View Page Header
        include(VIEWS."/back/instructor/header.html");

        // View Page Content 
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/profile.html");
        
        // View Page Footer
        include(VIEWS."/back/instructor/footer.html");
    }
    public function __destruct()
    {
       ob_end_flush();
    }
}