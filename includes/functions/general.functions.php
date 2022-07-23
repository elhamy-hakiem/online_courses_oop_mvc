<?php

function getErrors()
{
    if(isset($_SESSION['errors']) && count($_SESSION['errors']) > 0)
    {
        $errors = $_SESSION['errors'];
        $HTMLerrors = '<div class="alert alert-block alert-danger fade in">
                            <button data-dismiss="alert" class="close close-sm" type="button">
                                <i class="icon-remove"></i>
                            </button>';
        foreach($errors as $error)
        {
            $HTMLerrors .= '<span>'.$error.'</span>';
        }
        $HTMLerrors .= '</div>';

        $_SESSION['errors'] = [];
        return $HTMLerrors;
    }
    return null;
}


function getSuccessMsg()
{
    if( isset($_SESSION['successMsg']) && !empty($_SESSION['successMsg']) )
    {
        echo '<div class="alert alert-success alert-block fade in">
                <button data-dismiss="alert" class="close close-sm" type="button">
                    <i class="icon-remove"></i>
                </button>
                <h4>
                    <i class="icon-ok-sign"></i>
                    Success!
                </h4><span>';
            echo $_SESSION['successMsg'];
        echo '</span></div>';
        $_SESSION['successMsg'] = '';
    }
    return null;
}


/**
 * Hashed Password Function
 * @param password
 * @return string
 */
function hashPasswords($password)
{
    return password_hash($password,PASSWORD_DEFAULT);
}


/**
 * check login 
 */
function checkLogin()
{
    if(isset($_SESSION['user']))
        return true;
}

/**
 * redirect function
 */
function invalidRedirect($depth='')
{
    if ($_SESSION['user']['user_group'] == 1)
        Redirect::TO($depth.'admin/index.php');
    elseif ($_SESSION['user']['user_group'] == 2)
        Redirect::TO($depth.'instructor/index.php');
    elseif($_SESSION['user']['user_group'] == 3)
        Redirect::TO($depth.'student/index.php');
    else
        Redirect::TO($depth.'index.php');
}