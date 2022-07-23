<?php
class coursesLessonsCommentsModel extends model
{
    /**
     * add new comment
     * @param $dataArray
     * @return bool
     */
    public function addComment($data)
    {
        if(system::Get('db')->Insert('courses_lessons_comments',$data))
            return true;

        $this->setError("Error adding Comment : ".system::Get('db')->getDbErrors());
        return false;
    }
    /**
     * Update Comment
     * @param $commentId
     * @param $dataArray
     * @return bool
     */
    public function updateComment($commentId,$dataArray)
    {
        if(system::Get('db')->Update('courses_lessons_comments',$dataArray,"WHERE `comment_id` = $commentId"))
            return true;

        $this->setError("Error Update Comment : ".system::Get('db')->getDbErrors());
        return false;
    }
    /**
     * Delete Comment
     * @param $commentId
     * @return bool
     */
    public function deleteComment($commentId)
    {
        if(system::Get('db')->Delete('courses_lessons_comments',"WHERE `comment_id` = $commentId"))
            return true;

        $this->setError("Error Delete Comment : ".system::Get('db')->getDbErrors());
        return false;
    }
    /**
     * Get All Comments
     * @param string $extra 
     * @return $array
     */
    public function getComments($extra ='')
    {
        system::Get('db')->Execute("SELECT `courses_lessons_comments`.*,`users`.*,`courses_lessons`.`lesson_title` 
                                    FROM `courses_lessons_comments` 
                                    LEFT JOIN `users` 
                                    ON `courses_lessons_comments`.`comment_user` = `users`.`user_id` 
                                    LEFT JOIN `courses_lessons` 
                                    ON `courses_lessons_comments`.`comment_lesson` = `courses_lessons`.`lesson_id` $extra");
        if(system::Get('db')->AffectedRows() > 0)
            return system::Get('db')->GetRows();
        
        $this->setError("Error Get Comments : ".system::Get('db')->getDbErrors());
        return [];
    }
    /**
     * Get Comment BY Id
     * @param $commentId
     * @return $array
     */
    public function getCommentById($commentId)
    {
        $comments = $this->getComments("WHERE `comment_id`=$commentId");
        if(!empty($comments))
            return $comments[0];
        
        $this->setError("Error Get Comment By Id : ".system::Get('db')->getDbErrors());
        return [];
    }
    /**
     * Get comments BY lesson Id
     * @param $lessonId
     * @return $array
     */
    public function getCommentsByLessonId($lessonId)
    {
        return $this->getComments("WHERE `courses_lessons_comments`.`comment_lesson` = $lessonId");
    }
    /**
     * Get comments BY User Id
     * @param $userId
     * @return $array
     */
    public function getCommentsByUserId($userId)
    {
        return $this->getComments("WHERE `courses_lessons_comments`.`comment_user` = $userId");
    }
}