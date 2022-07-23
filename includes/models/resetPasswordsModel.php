<?php

class resetPasswordModel extends model
{
     /**
     * Add New code
     * @param $dataArray
     * @return bool
     */
    public function addCode($dataArray)
    {
        if(system::Get('db')->Insert('resetpasswords',$dataArray))
            return true;

        $this->setError("Error adding Code : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Delete Code
     * @param $Code String
     * @return bool
     */
    public function deleteCode($code)
    {
        if(system::Get('db')->Delete('resetpasswords',"WHERE `code` = '$code'"))
            return true;

        $this->setError("Error Delete Code : ".system::Get('db')->getDbErrors());
        return false;
    }
    /**
     * Get User Email 
     * @param  string $code
     * @return Array
     */
    public function getUserByCode($code)
    {
        system::Get('db')->Execute("SELECT * FROM   `resetpasswords` WHERE `code` = '$code' LIMIT 1");
        if(system::Get('db')->AffectedRows() > 0)
        {
            $user = system::Get('db')->GetRows();   
            return $user[0];
        }
        return [];
    }

}