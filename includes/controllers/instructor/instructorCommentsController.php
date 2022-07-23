<?php

class instructorCommentsController extends controller
{
    // private $usersModel ;
    //private $courseLessonsModel;
    private $courseModel;
    private $coursesLessonsCommentsModel; 

    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(2);
        $this->usersModel = new usersModel();
        $this->courseModel = new coursesModel();
        $this->courseLessonsModel = new coursesLessonsModel();
        $this->coursesLessonsCommentsModel = new coursesLessonsCommentsModel();
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
     * Start Add Course Lessons Comments
     */
    public function addComments()
    {
        $lessonId     = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? intval($_GET['lessonid']) :0;
        $instructorId = $_SESSION['user']['user_id'];

        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addComment']))
        {
            $title         = $_POST['commTitle'];
            $content       = $_POST['commContent'];
            #Validate Title . . . . 
            if(strlen($title) < 4)
            {
                $this->setControllerErrors("Comment Title Must Be At Least 4 chars");
            }
            elseif(strlen($content) < 10)
            {
                $this->setControllerErrors("comment Description Must Be At Least 10 chars");
            }
            else
            {
                $dataInputs = array(
                    "comment_title"        => $title,
                    "comment_content"      => $content,
                    "comment_lesson"       => $lessonId,
                    "comment_user"         => $instructorId
                );
                if($this->coursesLessonsCommentsModel->addComment($dataInputs))
                {
                    $this->setControllerSuccessMsg("Comment Added");
                }
                else
                {
                    $this->setControllerErrors($this->coursesLessonsCommentsModel->getError());
                }
            }
        }
    }


    /**
     * Start Delete Course Lessons Comments
     */
    public function deleteComments()
    {
        $courseId     = isset($_GET['courseid']) && is_numeric($_GET['courseid']) ? intval($_GET['courseid']) : 0;
        $lessonId     = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? intval($_GET['lessonid']) : 0;
        $commentId = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']):0;

        $lessons = $this ->courseLessonsModel->getLessons("WHERE `lesson_id` = $lessonId AND `lesson_course` = $courseId LIMIT 1 ");

        if(empty($lessons))
        {
            $this->PageNotFound();
            exit;
        }
        else
        {
            $comment = $this->coursesLessonsCommentsModel->getCommentById($commentId);
            if(empty($comment))
            {
                $this->PageNotFound();
                exit;
            }
            else
            {
                if($this->coursesLessonsCommentsModel->deleteComment($commentId))
                $this->setControllerSuccessMsg("Comment Deleted ");
                else
                    $this->setControllerErrors($this->coursesLessonsCommentsModel->getError());
            }
        }
    }
}