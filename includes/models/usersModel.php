<?php

class usersModel extends model
{
    private $userData;
    /**
     * add new user 
     * @param $dataArray
     * @return bool
     */
    public function addUser($dataArray)
    {
        if(system::Get('db')->Insert('users',$dataArray))
            return true;

        $this->setError("Error adding User : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * update user 
     * @param $userid
     * @param $dataArray
     * @return bool
     */
    public function updateUser($id,$dataArray)
    {
        if(system::Get('db')->Update('users',$dataArray,"WHERE `user_id` = $id"))
            return true;

        $this->setError("Error Update User : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Change Password By Email
     * @param $email
     * @param $dataArray
     * @return bool
     */
    public function changePassword($email,$dataArray)
    {
        if(system::Get('db')->Update('users',$dataArray,"WHERE `email` = '$email'"))
            return true;

        $this->setError("Error Change Password : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Delete user 
     * @param $userid
     * @param $dataArray
     * @return bool
     */
    public function deleteUser($id)
    {
        if(system::Get('db')->Delete('users',"WHERE `user_id` = $id"))
            return true;

        $this->setError("Error Delete User : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Get All users 
     * @param  string $extra
     * @return Array
     */
    public function getUsers($extra='')
    {
        system::Get('db')->Execute("SELECT `users`.*,`users_groups`.`group_name` 
                                    FROM   `users` 
                                    LEFT JOIN `users_groups` 
                                    ON `users`.`user_group` = `users_groups`.`group_id` $extra ");
        if(system::Get('db')->AffectedRows() > 0)
            return system::Get('db')->GetRows();
        return [];
    }

    /**
     * Get User By Id 
     * @param $userid
     * @return Array
     */
    public function getUser($id)
    {
       $user = $this->getUsers("WHERE `user_id` = $id LIMIT 1");
        if(count($user) > 0)
            return $user[0];
        else
            return false;
    }

     /**
     * Get users by group Id
     * @param $groupId
     * @param  string $extra
     * @return Array
     */
    public function getUsersByGroup($groupId,$extra='')
    {
        return $this->getUsers("WHERE `user_group` = $groupId $extra");
    }

     /**
     * Search users by Username Or Email
     * @param  string $keyword
     * @return Array
     */
    public function searchUsers($keyword)
    {
        return $this->getUsers("WHERE `users`.`username` LIKE '%$keyword%' OR `users`.`email` LIKE '%$keyword%'");
    }

     /**
     * Login user by Username And Password
     * @param string $username
     * @param string $password 
     * @return bool
     */
    public function login($username,$password)
    {
        $users = $this->getUsers("WHERE `users`.`username` = '$username' LIMIT 1");
        if(count($users) > 0)
        {
            $hashedPassword = $users[0]['password'];
            if(password_verify($password,$hashedPassword))
            {
                $this->userData = $users[0];
                return true;
            }
            $this->setError("Password Is Not Correct");
            return false;
        }
        else
        {
            $this->setError("Invalid Username Or Password");
            return false;
        }
    }

    /**
     * get user data
     * @return Array
     */
    public function getUserData()
    {
        return $this->userData;
    }

}