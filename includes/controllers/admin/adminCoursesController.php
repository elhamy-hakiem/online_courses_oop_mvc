<?php

class adminCoursesController extends controller
{
    private $coursesModel ;

    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(1);
        $this->coursesModel = new coursesModel();
    }
    /**
     * Start Get All Courses
    */
    public function getCourses()
    {
        $categoryid = isset($_GET['cid']) && is_numeric($_GET['cid']) ? intval($_GET['cid']) : 0;
        $userid     = isset($_GET['uid']) && is_numeric($_GET['uid']) ? intval($_GET['uid']) : 0;

        if($categoryid > 0)
            $courses = $this->coursesModel->getCourseByCategoryId($categoryid);
        elseif($userid > 0)
            $courses = $this->coursesModel->getCourseByInstructorId($userid);
        elseif($categoryid > 0 && $userid > 0)
            $courses  = $this->coursesModel ->getCourses("WHERE `courses`.`course_category` = $categoryid AND `courses`.`course_instructor` = $userid");
        else
            // model -> get all courses
            $courses  = $this->coursesModel ->getCourses();

        // View  -> display Courses
        include(VIEWS."/back/admin/header.html");
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/courses.html");
        include(VIEWS."/back/admin/footer.html");
    }
    
    /**
     * Start Search Courses
    */
    public function searchCourses()
    {
        $keyword = isset($_GET['q']) && is_string($_GET['q']) ? $_GET['q'] : 0;

        $courses = $this->coursesModel->searchCourse($keyword);
        
        // View  -> display Courses
        include(VIEWS."/back/admin/searchcourses.html");
    }

    /**
     * Start Delete Courses
    */
    public function deleteCourses()
    {
        $courseid = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;

        $courseData = $this->coursesModel->getCourseById($courseid);

        if(empty($courseData))
        {
            $this->PageNotFound();
            exit;
        }
        else
        {
            if($this->coursesModel->deleteCourse($courseid))
                $this ->setControllerSuccessMsg("Course Deleted");
            else
                $this->setControllerErrors($this->coursesModel->getError());
        }
        // get all courses 
        $this->getCourses();
    }

    public function __destruct()
    {
       ob_end_flush();
    }
}