<?php

class coursesStudentsModel extends model
{
    /**
     * add student to course
     * @param $studentId
     * @param $courseId
     * @return bool
     */
    public function addStudentToCourse($studentId,$courseId)
    {
        $data = array(
            'course_id'   => $courseId,
            'student_id'  => $studentId
        );
        if(system::Get('db')->Insert('courses_students',$data))
            return true;

        $this->setError("Error adding Student To Course : ".system::Get('db')->getDbErrors());
        return false;
    }
    /**
     * Delete student From course
     * @param $studentId
     * @param $courseId
     * @return bool
     */
    public function deleteStudentFromCourse($studentId,$courseId)
    {
        if(system::Get('db')->Delete('courses_students',"WHERE `course_id`=$courseId AND `student_id`=$studentId "))
            return true;

        $this->setError("Error Delete Student From Course : ".system::Get('db')->getDbErrors());
        return false;
    }
     /**
     * check if user joined course
     * @param $studentId
     * @param $courseId
     * @return bool
     */
    public function isStudentJoinedCourse($studentId,$courseId)
    {
        system::Get('db')->Execute("SELECT * FROM `courses_students` WHERE `course_id`=$courseId AND `student_id`=$studentId LIMIT 1");
        
        if(system::Get('db')->AffectedRows() > 0 )
            return true;

        return false;
    }
    /**
     * confirm  subscription
     * @param $studentId
     * @param $courseId
     * @return bool
     */
    public function confirmStudentSubscription($studentId,$courseId)
    {
        $data = array(
            'approved'   => 1
        );
        if(system::Get('db')->Update('courses_students',$data,"WHERE `course_id`=$courseId AND `student_id`=$studentId "))
            return true;

        return false;

    }
    /**
     * get students in course
     * @param $courseId
     * @param int $status
     * @return array
     */
    public function getStudentsByCourseId($courseId,$status=1)
    {
        system::Get('db')->Execute("SELECT `courses_students`.*,`courses`.`course_title`,`users`.* 
                                    FROM `courses_students` 
                                    LEFT JOIN `courses` 
                                    ON `courses_students`.`course_id` = `courses`.`course_id` 
                                    LEFT JOIN `users` 
                                    ON `courses_students`.`student_id` =`users`.`user_id` 
                                    WHERE `courses_students`.`course_id` = $courseId 
                                    AND `courses_students`.`approved`= $status");
        if(system::Get('db')->AffectedRows() > 0)
            return system::Get('db')->GetRows();

        return [];
    }
    /**
     * get courses for student by id
     * @param $studentId
     * @return array
     */
    public function getCoursesByStudentId($studentId)
    {
        system::Get('db')->Execute("SELECT `courses_students`.*,`courses`.`course_title`,`users`.`username` 
                                    FROM `courses_students` 
                                    LEFT JOIN `courses` 
                                    ON `courses_students`.`course_id` = `courses`.`course_id` 
                                    LEFT JOIN `users` 
                                    ON `courses_students`.`student_id` =`users`.`user_id` 
                                    WHERE `courses_students`.`student_id` = $studentId");

        if(system::Get('db')->AffectedRows() > 0)
        return system::Get('db')->GetRows();

        return [];
    }
}