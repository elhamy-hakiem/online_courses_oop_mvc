<?php

class coursesLessonsModel extends model
{
     /**
     * Add New Lesson
     * @param $dataArray
     * @return bool
     */
    public function addLesson($data)
    {
        if(system::Get('db')->Insert('courses_lessons',$data))
            return true;

        $this->setError("Error adding Lesson : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Update lesson
     * @param $lessonId
     * @param $dataArray
     * @return bool
     */
    public function updateLesson($lessonId,$dataArray)
    {
        if(system::Get('db')->Update('courses_lessons',$dataArray,"WHERE `lesson_id` = $lessonId"))
            return true;

        $this->setError("Error Update Lesson : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Delete lesson
     * @param $lessonId
     * @return bool
     */
    public function deleteLesson($lessonId)
    {
        if(system::Get('db')->Delete('courses_lessons',"WHERE `lesson_id` = $lessonId"))
            return true;

        $this->setError("Error Delete Lesson : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Get All Lessons
     * @param string $extra 
     * @return $array
     */
    public function getLessons($extra='')
    {
        system::Get('db')->Execute("SELECT `courses_lessons`.*,`courses`.`course_title`,`users`.`username` 
                                    FROM `courses_lessons` 
                                    LEFT JOIN `courses` 
                                    ON `courses_lessons`.`lesson_course` = `courses`.`course_id` 
                                    LEFT JOIN `users` 
                                    ON `courses_lessons`.`lesson_instructor` = `users`.`user_id` $extra");
        if(system::Get('db')->AffectedRows() > 0)
            return system::Get('db')->GetRows();
        return [];
    }

     /**
     * Get Lesson BY Id
     * @param $lessonId
     * @return $array
     */
    public function getLessonById($lessonId)
    {
        $lessons = $this->getLessons("WHERE `lesson_id`=$lessonId");
        if(!empty($lessons))
            return $lessons[0];
        return [];
    }

    /**
     * Get lessons BY course Id
     * @param $courseId
     * @return $array
     */
    public function getLessonsByCourseId($courseId)
    {
        return $this->getLessons("WHERE `courses_lessons`.`lesson_course` = $courseId");
    }

    /**
     * Get lessons BY Instructor Id
     * @param $instructorId
     * @return $array
     */
    public function getLessonsByInstructorId($instructorId)
    {
        return $this->getLessons("WHERE `courses_lessons`.`lesson_instructor` = $instructorId");
    }

    /**
     * Search lesson by Title
     * @param $keyword
     * @return $array
     */
    public function searchLesson($keyword)
    {
        $lessons = $this->getLessons("WHERE `lesson_title` LIKE '$keyword%'");
        if(!empty($lessons))
            return $lessons;
        return [];
    }
}