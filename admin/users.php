<?php
require("../globals.php");
require(MODELS."/usersModel.php");
require(MODELS."/usersGroupsModel.php");
require(ADMINCONTROLLER."/adminUsersController.php");


$adminUsersController = new adminUsersController();


// Start define action 
$action= isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage' ;
// End define action 

// start manage Users 
if($action == 'manage')
{
   $adminUsersController ->getUsers();
}
// End manage Users

// start Add Users 
elseif($action == 'add')
{
   $adminUsersController ->addUser();
}
// End Add Users

// start Update Users 
elseif($action == 'update')
{
   $adminUsersController ->updateUser();
}
// End Update Users

// start Show Profile 
elseif($action == 'profile')
{
   $adminUsersController ->showProfile();
}
// End Show Profile 

// start Delete Users 
elseif($action == 'delete')
{
   $adminUsersController ->deleteUser();
}
// End Delete Users

// start search Users 
// elseif($action == 'search')
// {
//     $adminUsersController ->searchUsers();
// }
// End Search Users 

else
{
    $adminUsersController -> PageNotFound();
}