<?php
require("../globals.php");
require(MODELS."/usersGroupsModel.php");
require(ADMINCONTROLLER."/adminUsersGroupsController.php");


$adminUserGroupsController = new adminUsersGroupsController();


// Start define action 
$action= isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage' ;
// End define action 


// start manage Users Groups
if($action == 'manage')
{
   $adminUserGroupsController ->getUsersGroups();
}
// End manage Users Groups

// Start Add Users Groups
elseif($action == 'add')
{
   $adminUserGroupsController ->addUserGroup();
}
// End Add Users Groups

// Start Update Users Groups
elseif($action == 'update')
{
   $adminUserGroupsController ->updateUserGroup();
}
// End Update Users Groups


// start Delete Users Groups
elseif($action == 'delete')
{
   $adminUserGroupsController ->deleteUsersGroups();
}
// End Delete Users Groups


else
{
    $adminUserGroupsController -> PageNotFound();
}