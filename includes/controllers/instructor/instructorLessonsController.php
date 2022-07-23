<?php
class instructorLessonsController extends controller
{
    private $courseLessonsModel;
    private $courseModel;
    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(2);
        $this->courseModel        = new coursesModel();
        $this->courseLessonsModel = new coursesLessonsModel();
    }

    /**
     * Check If Instructor Have Premission To Access The Course 
     */
    public function checkInstructorHaveCourse($courseId)
    {
        $courseData = $this->courseModel->getCourseById($courseId);
        if(empty($courseData) || $courseData['course_instructor'] != $_SESSION['user']['user_id'])
        {
            $this->PageNotFound();
            exit;
        }
    }

    /**
     * Start Get Course Lessons
     */
    public function getCourseLessons()
    {
        $courseId = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;

       // Check If Instructor Have Premission To Access The Course 
       $this->checkInstructorHaveCourse($courseId);

       $courseData = $this->courseModel->getCourseById($courseId);

        $lessons = $this->courseLessonsModel->getLessonsByCourseId($courseId);
        // display lessons 
        include(VIEWS."/back/instructor/header.html");
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/courselessons.html");
        include(VIEWS."/back/instructor/footer.html");
    }

    /**
     * Start Add Course Lessons
     */
    public function addCourseLessons()
    {
        $courseId     = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;

        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        if(isset($_POST['addLesson']))
        {
            $filterTitle      = $_POST['lessonTitle'];
            $filterDesc       = $_POST['lessonDescription'];
            $filterInstructor = $_SESSION['user']['user_id'];
            $filterUrl        = $_POST['lessonUrl'];

            $lessonCover        = $_FILES['lessonCover'];

            // Check If Lesson Title Already Exist 
            $checkTitle = $this->courseLessonsModel->getLessons("WHERE `lesson_title` = '$filterTitle' AND `lesson_course` = $courseId LIMIT 1");
            
            if(!empty($checkTitle))
            {
                $this->setControllerErrors("Lesson Name Already Exist");
            }
            elseif(strlen($filterTitle) < 4)
            {
                $this->setControllerErrors("Lesson Title Must Be At Least 4 chars");
            }
            elseif(strlen($filterDesc) < 10)
            {
                $this->setControllerErrors("Lesson Description Must Be At Least 10 chars");
            }
            elseif(! filter_var($filterUrl,FILTER_VALIDATE_URL))
            {
                $this->setControllerErrors("Lesson URL Not Valid !");
            }
            else
            {
                // Create New Object From Upload Image Class 
                $uploadImageObj   = new uploadImage($lessonCover);
                $uploadImageObj -> validateImage();

                if(!empty($uploadImageObj->getImageErrors()))
                {
                    $this->setControllerErrors($uploadImageObj->getImageErrors());
                }
                else
                {
                    if($uploadImageObj->uploadFile(UPLOADS."/lessons"))
                    {
                        $newImageName = $uploadImageObj->newImageName;
                        $lessonData = array(
                            'lesson_title'         => $filterTitle,
                            'lesson_description'   => $filterDesc,
                            'lesson_cover'         => $newImageName,
                            'lesson_video'         => $filterUrl,
                            'lesson_instructor'    => $filterInstructor,
                            'lesson_course'        => $courseId
                        );

                        if($this ->courseLessonsModel->addLesson($lessonData))
                        {
                            $this->setControllerSuccessMsg("Lesson Added");
                        }
                        else
                        {
                            $this->setControllerErrors($this->courseLessonsModel->getError());
                        }
                    }
                    else
                    {
                        $this->setControllerErrors($uploadImageObj->getImageErrors());
                    }
                }
            }
        }
        
        // display Add Form 
        include(VIEWS."/back/instructor/header.html");
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/addlesson.html");
        include(VIEWS."/back/instructor/footer.html");
    }

    /**
     * Start Update Course Lessons
     */
    public function updateCourseLessons()
    {
        $courseId     = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;
        $lessonId     = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? intval($_GET['lessonid']) : 0;

       // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        $lessons = $this ->courseLessonsModel->getLessons("WHERE `lesson_id` = $lessonId AND `lesson_course` = $courseId LIMIT 1 ");

        if(empty($lessons))
        {
            $this->PageNotFound();
            exit;
        }

        // get lesson data if not empty lessons 
        $lessonData = $lessons[0];

        if(isset($_POST['updateLesson']))
        {
            $filterTitle      = filter_var($_POST['lessonTitle'],FILTER_SANITIZE_STRING);
            $filterDesc       = filter_var($_POST['lessonDescription'],FILTER_SANITIZE_STRING);
            $filterInstructor = filter_var($_SESSION['user']['user_id'],FILTER_SANITIZE_NUMBER_INT);
            $filterUrl        = filter_var($_POST['lessonUrl'],FILTER_SANITIZE_URL);

            $oldCover         = $_POST['oldCover'];
            $newCoverName     ='';

            $lessonCover        = $_FILES['lessonCover'];
            $lessonCoverName    = $lessonCover['name'];

            $updateErrors = 0;

            // Check If Lesson Title Already Exist 
            $checkTitle = $this->courseLessonsModel->getLessons("WHERE `lesson_title` = '$filterTitle' AND `lesson_course` = $courseId AND `lesson_id` != $lessonId LIMIT 1");
             
            if(!empty($checkTitle))
            {
                $this->setControllerErrors("Lesson Name Already Exist");
                $updateErrors +=1;
            }
            if(strlen($filterTitle) < 4)
            {
                $this->setControllerErrors("Lesson Title Must Be At Least 4 chars");
                $updateErrors +=1;
            }
            if(strlen($filterDesc) < 10)
            {
                $this->setControllerErrors("Lesson Description Must Be At Least 10 chars");
                $updateErrors +=1;
            }
            if(! filter_var($filterUrl,FILTER_VALIDATE_URL))
            {
                $this->setControllerErrors("Lesson URL Not Valid !");
                $updateErrors +=1;
            }
            //Start Cover Validate
            if(empty($lessonCoverName))
            {
                if(empty($oldCover))
                {
                    $this->setControllerErrors("Cover Can't Be Empty ! ");
                    $updateErrors += 1 ;
                }
                $newCoverName = $oldCover; 
            }
            if(!empty($lessonCoverName))
            {
                 // Create New Object From Upload Image Class 
                 $uploadImageObj   = new uploadImage($lessonCover);
                 $uploadImageObj -> validateImage();

                if(!empty($uploadImageObj->getImageErrors()))
                {
                    $this->setControllerErrors($uploadImageObj->getImageErrors());
                    $updateErrors += 1 ;
                }
                else
                {
                    if($uploadImageObj->uploadFile(UPLOADS."/lessons"))
                    {
                        $newCoverName = $uploadImageObj->newImageName;
                    }
                    else
                    {
                        $this->setControllerErrors($uploadImageObj->getImageErrors());
                        $updateErrors += 1 ;
                    }
                }
            }
            if($updateErrors == 0)
            {
               
                $lessonData = array(
                    'lesson_title'         => $filterTitle,
                    'lesson_description'   => $filterDesc,
                    'lesson_cover'         => $newCoverName,
                    'lesson_video'         => $filterUrl,
                    'lesson_instructor'    => $filterInstructor,
                    'lesson_course'        => $courseId
                );

                if($this ->courseLessonsModel->updateLesson($lessonId,$lessonData))
                {
                    $this->setControllerSuccessMsg("Lesson Updated");
                }
                else
                {
                    $this->setControllerErrors($this->courseLessonsModel->getError());
                }

            }
        }
        
        // display Update Form 
        include(VIEWS."/back/instructor/header.html");
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/updatelesson.html");
        include(VIEWS."/back/instructor/footer.html");
    }

    /**
     * Start Delete Course Lessons
     */
    public function deleteCourseLessons()
    {
        $courseId     = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;
        $lessonId     = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? intval($_GET['lessonid']) : 0;
    
        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        $lessons = $this ->courseLessonsModel->getLessons("WHERE `lesson_id` = $lessonId AND `lesson_course` = $courseId LIMIT 1 ");

        if(empty($lessons))
        {
            $this->PageNotFound();
            exit;
        }
        else
        {
            if($this->courseLessonsModel->deleteLesson($lessonId))
                $this->setControllerSuccessMsg("Lesson Deleted ");
            else
                $this->setControllerErrors($this->courseLessonsModel->getError());
        }

        // display Course Lessons
        $this->getCourseLessons();
    }

    /**
     * Start View Lesson Details
     */
    public function viewLessonDetails()
    {
        $courseId     = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;
        $lessonId     = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? intval($_GET['lessonid']) : 0;

        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        $lessons = $this ->courseLessonsModel->getLessons("WHERE `lesson_id` = $lessonId AND `lesson_course` = $courseId LIMIT 1 ");

        if(empty($lessons))
        {
            $this->PageNotFound();
            exit;
        }

        // get lesson data if not empty lessons 
        $lessonData = $lessons[0];
        $coursesLessonsCommentsModel = new coursesLessonsCommentsModel();
        $lessonComments = $coursesLessonsCommentsModel->getCommentsByLessonId($lessonId);

        // display lesson details
        include(VIEWS."/back/instructor/header.html");
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/lessondetails.html");
        include(VIEWS."/back/instructor/footer.html");
        
    }
    
    public function __destruct()
    {
        ob_end_flush();
    }

}