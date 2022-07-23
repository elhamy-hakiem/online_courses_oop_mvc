<?php

class coursesCategoriesModel extends model
{
    /**
     * Add New Category
     * @param $dataArray
     * @return bool
     */
    public function addCategory($dataArray)
    {
        if(system::Get('db')->Insert('courses_categories',$dataArray))
            return true;
        
        $this->setError("Error adding Category : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Update Category
     * @param $categoryId
     * @param $dataArray
     * @return bool
     */
    public function updateCategory($categoryId,$dataArray)
    {
        if(system::Get('db')->Update('courses_categories',$dataArray,"WHERE `category_id` = $categoryId"))
            return true;

        $this->setError("Error Update Category : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Delete Category
     * @param $categoryId
     * @return bool
     */
    public function deleteCategory($categoryId)
    {
        if(system::Get('db')->Delete('courses_categories',"WHERE `category_id` = $categoryId"))
            return true;
        
        $this->setError("Error Delete Category : ".system::Get('db')->getDbErrors());
        return false;
    }

    /**
     * Get All Category
     * @param string $extra 
     * @return $array
     */
    public function getCategories($extra='')
    {
        system::Get('db')->Execute("SELECT `courses_categories`.*,`users`.`username` 
                                    FROM `courses_categories` 
                                    LEFT JOIN `users` 
                                    ON `courses_categories`.`created_by` = `users`.`user_id` $extra");
        if(system::Get('db')->AffectedRows() > 0)
            return system::Get('db')->GetRows();
        return [];
    }

     /**
     * Get Category BY Id
     * @param $categoryId
     * @return $array
     */
    public function getCategoryById($categoryId)
    {
        $categories = $this->getCategories("WHERE `category_id`=$categoryId");
        if(count($categories) > 0)
            return $categories[0];
        return [];
    }

     /**
     * Search Category by Name
     * @param $keyword
     * @return $array
     */
    public function searchCategory($keyword)
    {
        $categories = $this->getCategories("WHERE `category_name` LIKE '$keyword%'");
        if(count($categories) > 0)
            return $categories;
        return [];
    }
}