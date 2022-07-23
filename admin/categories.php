<?php
require("../globals.php");
require(MODELS."/coursesCategoriesModel.php");
require(ADMINCONTROLLER."/adminCategoriesController.php");

// Start page 
$adminCategoriesController = new adminCategoriesController();

// Start define action 
$action= isset($_GET['action']) && !empty($_GET['action']) ? $_GET['action'] : 'manage' ;
// End define action 


// start manage Category
if($action == 'manage')
{
    $adminCategoriesController -> getCategories();
}
// End manage Category

// start Add Category
elseif($action == 'add')
{
    $adminCategoriesController -> addCategory();
}
// End Add Category

// start Update Category
elseif($action == 'update')
{
    $adminCategoriesController -> updateCategory();
}
// End Update Category

// start Delete Category
elseif($action == 'delete')
{
    $adminCategoriesController -> deleteCategory();
}
// End Delete Category


else
{
    $adminCategoriesController -> PageNotFound();
}