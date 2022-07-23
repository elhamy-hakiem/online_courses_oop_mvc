<?php
class model
{
    private $errors =array();

    // Set Error In Array 
     public function setError($error)
     {
         if(is_array($error))
         {
             foreach($error as $oneError)
             {
                $this->errors[]= $oneError;
             }
         }
        else
            $this->errors[] = $error;
     }

    //  Get All Error 
     public function getError()
     {
         return $this->errors;
     }
}