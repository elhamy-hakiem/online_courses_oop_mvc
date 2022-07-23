<?php

class usersGroupsModel extends model
{

    /**
     * Add new User Group
     * @param $groupName
     */
    public function addUserGroup($groupName)
    {
        $data = array(
            'group_name' =>$groupName
        );
        if(system::Get('db')->Insert('users_groups',$data))
            return true;

        $this->setError("Error adding User Group : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Update User Group
     * @param $groupName
     * @param $groupId
     */
    public function updateUserGroup($groupId,$data)
    {
        if(system::Get('db')->Update('users_groups',$data,"WHERE `group_id`= $groupId"))
            return true;

        $this->setError("Error Update User Group : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Delete User Group
     * @param $groupId
     */
    public function deleteUserGroup($groupId)
    {
        if(system::Get('db')->Delete('users_groups',"WHERE `group_id`= $groupId"))
            return true;

        $this->setError("Error Delete User Group : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Get All User Group
     */
    public function getUsersGroups($extra='')
    {
       system::Get('db')->Execute("SELECT * FROM `users_groups` $extra");
       $groups = array();
       if(system::Get('db')->AffectedRows() > 0)
       {
            $groups = system::Get('db') ->GetRows();
       }
       return $groups;
    }

    /**
     * Get User Group By ID 
     * @param $groupId
     */
    public function getUserGroupById($groupId)
    {
        system::Get('db')->Execute("SELECT * FROM `users_groups` WHERE `group_id` = $groupId");
        $groups = array();
        if(system::Get('db')->AffectedRows() > 0)
        {
             $groups = system::Get('db') ->GetRow();
        }
        return $groups;
    }
}