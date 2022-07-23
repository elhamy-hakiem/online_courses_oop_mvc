<?php
class instructorStudentsController extends controller
{
    private $courseStudentsModel;
    private $courseModel;
    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(2);
        $this->courseModel = new coursesModel();
        $this->courseStudentsModel = new coursesStudentsModel();
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
     * Get All Course Students
     */
    public function getStudents()
    {
        $courseId = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;

        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        $courseData = $this->courseModel->getCourseById($courseId);

        if(isset($_GET['approve']) && $_GET['approve'] == 0)
        {
            $students = $this->courseStudentsModel->getStudentsByCourseId($courseId,0);
        }
        else
        {
            $students = $this->courseStudentsModel->getStudentsByCourseId($courseId);
        }

        // display Students 
        include(VIEWS."/back/instructor/header.html");
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/students.html");
        include(VIEWS."/back/instructor/footer.html");
    }

    /**
     * Start Approve Students
     */
    public function approveStudents()
    {
        $courseId = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;
        $studentId = isset($_GET['studentid']) && is_numeric($_GET['studentid']) ? intval($_GET['studentid']) : 0;

       // Check If Instructor Have Premission To Access The Course 
       $this->checkInstructorHaveCourse($courseId);

        if($this->courseStudentsModel->isStudentJoinedCourse($studentId,$courseId))
        {
            if($this->courseStudentsModel->confirmStudentSubscription($studentId,$courseId))
            {
                $this->setControllerSuccessMsg("Student Approved");
                // header("refresh:2;url=coursestudents.php?action=manage&courseid=".$courseId."&approve=0");
            }
            else
                $this->setControllerErrors($this->courseStudentsModel->getError());
        }
        else
        {
            $this->setControllerErrors("Student Not Joined In This Course ! ");
        }  
        
        // View All Approved Students 
        $this->getStudents();
    }

    /**
     * Start Delete Students From Course
     */
    public function deleteStudentFromCourse()
    {
        $courseId = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;
        $studentId = isset($_GET['studentid']) && is_numeric($_GET['studentid']) ? intval($_GET['studentid']) : 0;

        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        if($this->courseStudentsModel->isStudentJoinedCourse($studentId,$courseId))
        {
            if($this->courseStudentsModel->deleteStudentFromCourse($studentId,$courseId))
            {
                $this->setControllerSuccessMsg("Student Deleted");
            }
            else
                $this->setControllerErrors($this->courseStudentsModel->getError());
        }
        else
        {
            $this->setControllerErrors("Student Not Joined In This Course ! ");
        }  
        
        // View All Approved Students 
        $this->getStudents();

    }

    /**
     * Start View Student Details
     */
    public function viewStudent()
    {
        $courseId = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;
        $studentId = isset($_GET['studentid']) && is_numeric($_GET['studentid']) ? intval($_GET['studentid']) : 0;

        // Check If Instructor Have Premission To Access The Course 
        $this->checkInstructorHaveCourse($courseId);

        if($this->courseStudentsModel->isStudentJoinedCourse($studentId,$courseId))
        {
            $studentModel = new usersModel();
            $studentData =  $studentModel->getUser($studentId);
            $courseData  = $this->courseModel->getCourseById($courseId);
        }
        else
        {
            $this->PageNotFound();
            exit;
        }

        // Display Student Details
        include(VIEWS."/back/instructor/header.html");
        include(VIEWS."/back/instructor/sidebar.html");
        include(VIEWS."/back/instructor/studentdetails.html");
        include(VIEWS."/back/instructor/footer.html");
    }

    public function __destruct()
    {
        ob_end_flush();
    }
}