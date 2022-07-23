<?php

class instructorCoursesController extends controller
{
    private $coursesModel ;
    private $coursesCategoriesModel; 

    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(2);
        $this->coursesModel = new coursesModel();
        $this->coursesCategoriesModel = new coursesCategoriesModel();
    }

    /**
     * Check If Instructor Have Premission To Access The Course 
     */
    public function checkInstructorHaveCourse($courseId)
    {
        $courseData = $this->coursesModel->getCourseById($courseId);
        if(empty($courseData) || $courseData['course_instructor'] != $_SESSION['user']['user_id'])
        {
            $this->PageNotFound();
            exit;
        }
    }


    /**
     * get courses By Instructor Id 
     * @param $instructorId
     * @return Array
     */
    public function getInstructorCourses()
    {
        $instructorId = $_SESSION['user']['user_id'];
        $categoryid = isset($_GET['cid']) && is_numeric($_GET['cid']) ? intval($_GET['cid']) : 0;


        if($categoryid > 0)
            $courses = $this->coursesModel->getCourses("WHERE `courses`.`course_category` = $categoryid AND `courses`.`course_instructor` = $instructorId ORDER BY `courses`.`course_id` DESC ");
        else
            $courses = $this->coursesModel->getCourseByInstructorId($instructorId);

        // View Page Header
        include(VIEWS."/back/instructor/header.html");

        // View Page Content 
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/courses.html");
        
        // View Page Footer
        include(VIEWS."/back/instructor/footer.html");
    }

    
    /**
     * View course Details
     */
    public function viewCourse()
    {
        $courseId = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']):0;

        $courseData = $this->coursesModel->getCourseById($courseId);

        // get Number Of Course Lessons 
        $courseLessonsModel = new coursesLessonsModel();
        $numCourseLessons   = count( $courseLessonsModel->getLessonsByCourseId($courseId));

        // Get Number Of Course Students
        $courseStudentsModel = new coursesStudentsModel();
        $numCourseStudents   = count($courseStudentsModel->getStudentsByCourseId($courseId));
        // Students Waiting Approves 
        $numStudentsWaitingApproved   = count($courseStudentsModel->getStudentsByCourseId($courseId,0));

        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        // View Page Header
        include(VIEWS."/back/instructor/header.html");
        // View Page Content 
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/coursedetails.html");
        // View Page Footer
        include(VIEWS."/back/instructor/footer.html");
    }


   /**
     * Start Add New Course
    */
    public function addCourse()
    {
        $categories = $this->coursesCategoriesModel-> getCategories();

        if(isset($_POST['addcourse']))
        {
            $filterTitle      = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
            $filterDesc       = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
            $filterInstructor = filter_var($_SESSION['user']['user_id'],FILTER_SANITIZE_NUMBER_INT);
            $filterCategory   = filter_var($_POST['courseCategory'],FILTER_SANITIZE_NUMBER_INT);

            $coverData        = $_FILES['CourseCover'];

            // Check If Course Title Already Exist 
            $checkTitle = $this->coursesModel->getCourses("WHERE `course_title` = '$filterTitle' LIMIT 1");

            if(count($checkTitle) > 0)
            {
                $this->setControllerErrors("Course Name Already Exist");
            }
            elseif(strlen($filterDesc) < 10)
            {
                $this->setControllerErrors("Course Description Must Be At Least 10 chars");
            }
            elseif(strlen($filterTitle) < 4)
            {
                $this->setControllerErrors("Course Title Must Be At Least 4 chars");
            }
            elseif($filterCategory == 0  || empty($filterCategory))
            {
                $this->setControllerErrors("You Must Choose Course Category");
            }
            else
            {
                // Create New Object From Upload Image Class 
                $uploadImage      = new uploadImage($coverData);
                
                $uploadImage->validateImage();
                if(count($uploadImage->getImageErrors()) > 0)
                {
                   $this->setControllerErrors($uploadImage->getImageErrors());
                }
                else
                {
                    if($uploadImage->uploadFile(UPLOADS."/courses"))
                    {
                        $newImageName = $uploadImage->newImageName;
                        
                        $courseData = array(
                            'course_title'        => $filterTitle,
                            'course_description'  => $filterDesc,
                            'course_cover'        => $newImageName,
                            'course_instructor'   => $filterInstructor,
                            'course_category'     => $filterCategory
                        );
                        if($this->coursesModel->addCourse($courseData))
                        {
                            $this->setControllerSuccessMsg("Course Added");
                        }
                        else
                        {
                            $this->setControllerErrors($this->coursesModel->getError());
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
        include(VIEWS."/back/instructor/header.html");
        // View Page Content 
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/addcourse.html");
        include(VIEWS."/back/instructor/footer.html");
    }

    /**
     * Start Update Course
    */
    public function updateCourse()
    {
        // get course id 
        $courseId = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;

        $courseData = $this->coursesModel->getCourseById($courseId);

        $categories = $this->coursesCategoriesModel-> getCategories();

        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);
      
        if(isset($_POST['updatecourse']))
        {
            $filterTitle      = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
            $filterDesc       = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
            $filterCategory   = filter_var($_POST['courseCategory'],FILTER_SANITIZE_NUMBER_INT);

            $oldCover         = $_POST['oldcover'];
            $newCoverName     ='';

            $coverData        = $_FILES['CourseCover'];
            $coverName        = $coverData['name'];

            $updateErrors     = 0;

            // Check If Course Title Already Exist 
            $checkTitle = $this->coursesModel->getCourses("WHERE `course_title` = '$filterTitle' AND `course_id` !=$courseId LIMIT 1");

            if(count($checkTitle) > 0)
            {
                $this->setControllerErrors("Course Name Already Exist");
                $updateErrors +=1;
            }
            if(strlen($filterDesc) < 10)
            {
                $this->setControllerErrors("Course Description Must Be At Least 10 chars");
                $updateErrors +=1;
            }
            if(strlen($filterTitle) < 4)
            {
                $this->setControllerErrors("Course Title Must Be At Least 4 chars");
                $updateErrors +=1;
            }
            if($filterCategory == 0  || empty($filterCategory))
            {
                $this->setControllerErrors("You Must Choose Course Category");
                $updateErrors +=1;
            }
            //Start Cover Validate
            if(empty($coverName))
            {
                if(empty($oldCover))
                {
                    $this->setControllerErrors("Cover Can't Be Empty ! ");
                    $updateErrors += 1 ;
                }
                $newCoverName = $oldCover; 
            }
            if(!empty($coverName))
            {
                // new Object From Class Upload Image 
                $uploadImage      = new uploadImage($coverData);

                $uploadImage->validateImage();
                if(count($uploadImage->getImageErrors()) > 0)
                {
                    $this->setControllerErrors($uploadImage->getImageErrors());
                    $updateErrors += 1 ;
                }
                else
                {
                    if($uploadImage->uploadFile(UPLOADS."/courses"))
                    {
                        $newCoverName = $uploadImage->newImageName;
                    }
                    else
                    {
                        $this->setControllerErrors($uploadImage->getImageErrors());
                        $updateErrors += 1 ;
                    }
                }
            }
            if($updateErrors == 0)
            {
                $courseData = array(
                    'course_title'        => $filterTitle,
                    'course_description'  => $filterDesc,
                    'course_cover'        => $newCoverName,
                    'course_category'     => $filterCategory
                );
                if($this->coursesModel->updateCourse($courseId,$courseData))
                {
                    $this->setControllerSuccessMsg("Course Updated");
                }
                else
                {
                    $this->setControllerErrors($this->coursesModel->getError());
                }
            }
        }

        // View Page Header
        include(VIEWS."/back/instructor/header.html");
        // View Page Content 
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/updatecourse.html");
        include(VIEWS."/back/instructor/footer.html");
    }

    /**
     * Start Delete Course
     */
    public function deleteCourse()
    {
        // get course id 
        $courseId = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;

        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        if($this->coursesModel->deleteCourse($courseId))
        {
            $this->setControllerSuccessMsg("Course Deleted");
        }
        else
        {
            $this->setControllerErrors($this->coursesModel->getError());
        }
        // View All Courses 
        $this->getInstructorCourses();
    }


    public function __destruct()
    {
        ob_end_flush();
    }
}