<?php

class adminUsersGroupsController extends controller
{
    private $usersGroupsModel ;

    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(1);
        $this->usersGroupsModel = new usersGroupsModel();
    }

    /**
     * Start get all Users Groups
     */
    public function getUsersGroups()
    {
        // model -> get all Users Groups
        $groups = $this->usersGroupsModel ->getUsersGroups(); 

       
        // View  -> display Users Groups
        include(VIEWS."/back/admin/header.html");
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/usersgroups.html");
        include(VIEWS."/back/admin/footer.html");
    }

    /**
     * Start Add New User Group
    */
    public function addUserGroup()
    {
        if(isset($_POST['addgroup']))
        {
            // filter string Group Name 
            $filterGroupName = filter_var($_POST['groupname'],FILTER_SANITIZE_STRING);

            // Check Group Name In Database
            $checkGroupName = $this->usersGroupsModel->getUsersGroups("WHERE `group_name` = '$filterGroupName' LIMIT 1");

            if(count($checkGroupName) > 0 )
            {
                $this->setControllerErrors("Group Name Is Already Exist ");
            }
            else
            {
                if(strlen($filterGroupName) < 4 )
                {
                    $this->setControllerErrors("Group Name Must Be At Least 4 chars");
                }
                else
                {
                    if($this->usersGroupsModel->addUserGroup($filterGroupName))
                        $this->setControllerSuccessMsg("Group Added");
                    else
                        $this->setControllerErrors($this->usersGroupsModel->getError());
                }
            }
        }

        // View Page Header
        include(VIEWS."/back/admin/header.html");
        // View Page Content 
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/addusergroup.html");
        include(VIEWS."/back/admin/footer.html");
    }

    /**
     * Start Update User Group
    */
    public function updateUserGroup()
    {
        $groupid = isset($_GET['gid']) && is_numeric($_GET['gid']) ? intval($_GET['gid']) : 0;
        $groupData = $this->usersGroupsModel->getUserGroupById($groupid);

        if(empty($groupData))
        {
            $this->PageNotFound();
            exit;
        }
        if(isset($_POST['updategroup']))
        {
            $filterGroupName = filter_var($_POST['groupname'],FILTER_SANITIZE_STRING);

            // Check Group Name In Database
            $checkGroupName = $this->usersGroupsModel->getUsersGroups("WHERE `group_name` = '$filterGroupName' AND `group_id` != $groupid LIMIT 1");

            if(count($checkGroupName) > 0)
            {
                $this->setControllerErrors("Group Name Is Already Exist");
            }
            else
            {
                if(strlen($filterGroupName) < 4 )
                {
                    $this->setControllerErrors("Group Name Must Be At Least 4 chars");
                }
                else
                {
                    // prepare Data 
                    $groupData = array(
                        'group_name' =>$filterGroupName
                    );
                    if($this->usersGroupsModel->updateUserGroup($groupid,$groupData))
                        $this->setControllerSuccessMsg("Group Updated");
                    else
                        $this->setControllerErrors($this->usersGroupsModel->getError());
                }
            }
        }
        // View Page Header
        include(VIEWS."/back/admin/header.html");
        // View Page Content 
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/updateusergroup.html");
        include(VIEWS."/back/admin/footer.html");
    }


    /**
     * Start Delete Users Groups
    */
    public function deleteUsersGroups()
    {
        $groupid = isset($_GET['gid']) && is_numeric($_GET['gid']) ? intval($_GET['gid']) : 0;
        $userGroup = $this->usersGroupsModel->getUserGroupById($groupid);

        if(empty($userGroup))
        {
            $this->PageNotFound();
            exit;
        }

        if($this->usersGroupsModel->deleteUserGroup($groupid))
        {
            $this->setControllerSuccessMsg("Group Deleted");
        }
        else
        {
            $this->setControllerErrors($this->usersGroupsModel->getError());
        }

        // view All users Groups 
        $this->getUsersGroups();
    }

    public function __destruct()
    {
       ob_end_flush();
    }
}