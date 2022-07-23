<?php

class coursesModel extends model
{
     /**
     * Add New Course
     * @param $dataArray
     * @return bool
     */
    public function addCourse($dataArray)
    {
        if(system::Get('db')->Insert('courses',$dataArray))
            return true;

        $this->setError("Error adding Course : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Update Course
     * @param $courseId
     * @param $dataArray
     * @return bool
     */
    public function updateCourse($courseId,$dataArray)
    {
        if(system::Get('db')->Update('courses',$dataArray,"WHERE `course_id` = $courseId"))
            return true;

        $this->setError("Error Update Course : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Delete course
     * @param $courseId
     * @return bool
     */
    public function deleteCourse($courseId)
    {
        if(system::Get('db')->Delete('courses',"WHERE `course_id` = $courseId"))
            return true;

        $this->setError("Error Delete Course : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Get All Courses
     * @param string $extra 
     * @return $array
     */
    public function getCourses($extra='')
    {
        system::Get('db')->Execute("SELECT `courses`.*,`courses_categories`.`category_name`,`users`.`username` 
                                    FROM `courses` 
                                    LEFT JOIN `courses_categories` 
                                    ON `courses`.`course_category` = `courses_categories`.`category_id` 
                                    LEFT JOIN `users` 
                                    ON `courses`.`course_instructor` = `users`.`user_id` $extra");
        if(system::Get('db')->AffectedRows() > 0)
            return system::Get('db')->GetRows();
        return [];
    }

     /**
     * Get Course BY Id
     * @param $courseId
     * @return $array
     */
    public function getCourseById($courseId)
    {
        $courses = $this->getCourses("WHERE `course_id`=$courseId");
        if(count($courses) > 0)
            return $courses[0];
        return [];
    }

      /**
     * Get Course BY Category Id
     * @param $categoryId
     * @return $array
     */
    public function getCourseByCategoryId($categoryId)
    {
        return $this->getCourses("WHERE `courses`.`course_category` = $categoryId ORDER BY `courses`.`course_id` DESC ");
    }

    /**
     * Get Course BY Instructor Id
     * @param $instructorId
     * @return $array
     */
    public function getCourseByInstructorId($instructorId)
    {
        return  $this->getCourses("WHERE `courses`.`course_instructor` = $instructorId ORDER BY `courses`.`course_id` DESC ");
    }

    /**
     * Search Course by Title
     * @param $keyword
     * @return $array
     */
    public function searchCourse($keyword)
    {
        $courses = $this->getCourses("WHERE `course_title` LIKE '$keyword%'");
        if(count($courses) > 0)
            return $courses;
        return [];
    }
}