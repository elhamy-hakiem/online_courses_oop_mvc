$(document).ready(function () {
    // Search Course By Ajax 
    // $("#searchCourse").keyup(function (e) { 
    //     $.get("courses.php?action=search&q="+$(this).val(),function (data) {
    //             $("#coursesTable").html(data);
    //         }
    //     );
    // });

    // Search Users By Ajax 
    // $("#searchUsers").keyup(function (e) { 
    //     $.get("users.php?action=search&q="+$(this).val(),function (data) {
    //             $("#usersTable").html(data);
    //         }
    //     );
    // });

     // Install Users DataTable plugin 
     $('#usersTable').DataTable();

    // Install Users Groups DataTable plugin 
    $('#groupsTable').DataTable();

    // Install Categories DataTable plugin 
    $('#categoriesTable').DataTable();

    // Install Courses DataTable plugin 
    $('#coursesTable').DataTable();

    // Install Lessons DataTable plugin 
    $('#lessonsTable').DataTable();

    // Install Students DataTable plugin 
    // $('#studentsTable').DataTable();
    
    // Set Image Course Cover 
    var coverSource = $('#courseCoverHidden').val();
    if(coverSource == '')
    {
        coverSource = "default.jpg";
    }
    else
    {
        $(".alt.course-bg").css("background-image","linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,0.4)),url('../uploads/courses/"+coverSource+"')");
    }
});