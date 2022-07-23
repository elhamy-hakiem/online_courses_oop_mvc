<?php
class adminCategoriesController extends controller
{
    private $categoriesModel;

    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(1);
        $this->categoriesModel = new coursesCategoriesModel();
    }
    /**
     * Start get all Categories
     */
    public function getCategories()
    {
        // model -> get all categories
        $categories = $this->categoriesModel ->getCategories(); 

       
        // View  -> display categories
        include(VIEWS."/back/admin/header.html");
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/categories.html");
        include(VIEWS."/back/admin/footer.html");
    }


    /**
     * Start Add category
    */
    public function addCategory()
    {
        if(isset($_POST['addcategory']))
        {
            // filter string Category Name 
            $filterCategoryName = filter_var($_POST['catname'],FILTER_SANITIZE_STRING);

            // Check Category Name In Database 
            $checkCatName = $this->categoriesModel->getCategories("WHERE `category_name` = '$filterCategoryName' LIMIT 1");

            if(strlen($filterCategoryName) < 4)
            {
                $this->setControllerErrors("Category Name Must Be At Least 4 chars");
               
            }
            elseif(count($checkCatName) > 0)
            {
                $this->setControllerErrors("Category Name Is Already Exist ! ");
            }
            else
            {
                // prepare data 
                $categoryData = array(
                    'category_name' => $filterCategoryName,
                    'created_by'    => $_SESSION['user']['user_id']
                );

                // get model 
                if( $this->categoriesModel->addCategory($categoryData) )
                {
                    $this->setControllerSuccessMsg("Category Added");
                }
                else
                {
                    $this->setControllerErrors($this->categoriesModel->getError());
                }
            }
        }
        // View Page Header
        include(VIEWS."/back/admin/header.html");
        // View Page Content 
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/addcategory.html");
        include(VIEWS."/back/admin/footer.html");
    }
 
    /**
     * Start Update category
     */
    public function updateCategory()
    {
        $categoryId = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
        $categoryData = $this->categoriesModel->getCategoryById($categoryId);

        if(empty($categoryData))
        {
            $this->PageNotFound();
            exit;
        }

        if(isset($_POST['updatecategory']))
        {            
            // filter string Category Name 
            $filterCategoryName = filter_var($_POST['catname'],FILTER_SANITIZE_STRING);

            // Check Category Name In Database 
            $checkCatName = $this->categoriesModel->getCategories("WHERE `category_name` = '$filterCategoryName' AND category_id != $categoryId LIMIT 1");

            if(strlen($filterCategoryName) < 4)
            {
                $this->setControllerErrors("Category Name Must Be At Least 4 chars");
                
            }
            elseif(count($checkCatName) > 0)
            {
                $this->setControllerErrors("Category Name Is Already Exist ! ");
            }
            else
            {
                // prepare data 
                $categoryData = array(
                    'category_name' => $filterCategoryName
                );

                // get model 
                if( $this->categoriesModel->updateCategory($categoryId,$categoryData) )
                {
                    $this->setControllerSuccessMsg("Category Updated");
                }
                else
                {
                    $this->setControllerErrors($this->categoriesModel->getError());
                }
            }
        }
        // View Page Header
        include(VIEWS."/back/admin/header.html");
        // View Page Content 
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/updatecategory.html");
        include(VIEWS."/back/admin/footer.html");
    }
 

    /**
     * Start Delete category
    */
    public function deleteCategory()
    {
        $categoryId = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
        $categoryData = $this->categoriesModel->getCategoryById($categoryId);

        if(empty($categoryData))
        {
            $this->PageNotFound();
            exit;
        }

        if($this->categoriesModel->deleteCategory($categoryId))
        {
            $this->setControllerSuccessMsg("Category Deleted");
        }
        else
        {
            $this->setControllerErrors($this->categoriesModel->getError());
        }

        // view All Categories 
        $this->getCategories();
    }
 
     public function __destruct()
     {
        ob_end_flush();
     }
}