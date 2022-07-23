<?php

class adminCourseLessonsController extends controller
{
    private $lessonsModel ;

    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(1);
        $this->lessonsModel = new coursesLessonsModel();
    }
    /**
     * Start Get All Course Lessons
    */
    public function getCourseLessons()
    {
        $courseid = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;

        $courseModel = new coursesModel();
        //check if found course 
        $course = $courseModel->getCourseById($courseid);

        if(count($course) == 0)
        {
            $this->PageNotFound();
            exit;
        }
        else
        {
            // model -> get all course Lessons
            $lessons  = $this->lessonsModel ->getLessonsByCourseId($courseid);
        }

        // View  -> display Course Lessons
        include(VIEWS."/back/admin/header.html");
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/courselessons.html");
        include(VIEWS."/back/admin/footer.html");
    }

    public function deletelesson()
    {
        $lessonid = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? intval($_GET['lessonid']) : 0;
        
        $lesson = $this->lessonsModel->getLessonById($lessonid);

        if(empty($lesson))
        {
            $this->PageNotFound();
            exit;
        }
        else
        {
            if($this->lessonsModel->deleteLesson($lessonid))
                $this->setControllerSuccessMsg("Lesson Deleted");
            else
                $this->setControllerErrors($this->lessonsModel->getError());
        }
        // View  -> display Course Lessons
        $this->getCourseLessons();
    }

    public function __destruct()
    {
       ob_end_flush();
    }
}