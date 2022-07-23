<?php

class adminController extends controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkPermission(1);
    }
    public function index()
    {
        $usersModel      = new usersModel();
        $coursesModel    = new coursesModel();
        $categoriesModel = new coursesCategoriesModel();
        $lessonsModel    = new coursesLessonsModel();

        // total admins 
        $numAdmins      = count($usersModel->getUsersByGroup(1));
        // total instructors 
        $numInstructors = count($usersModel->getUsersByGroup(2));
        // total students
        $numStudents    = count($usersModel->getUsersByGroup(3));

        // Total Categories 
        $numCat = count($categoriesModel->getCategories());

        // Total Courses 
        $numCourses = count($coursesModel->getCourses());

        // Total Lessons 
        $numLessons = count($lessonsModel->getLessons());

        include(VIEWS."/back/admin/header.html");
        include(VIEWS."/back/admin/sidebar.html");
        include(VIEWS."/back/admin/index.html");
        include(VIEWS."/back/admin/footer.html");
    }

    public function __destruct()
    {
       ob_end_flush();
    }
}