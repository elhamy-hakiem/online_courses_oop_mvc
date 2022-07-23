<?php 

class uploadImage 
{
   private $imageName;
   private $imageSize;
   private $imageTemp;
   private $imageError;
   public $newImageName;
    
   private $imageErrors = array();

   // List Of Allowed File Type To Upload 
   private $imageAllowedExtension = array("jpeg","jpg","png","gif");

   /**
    * constructor Insert Image Data
    */
   public function __construct($imageData = array())
   {
       $this->imageName = $imageData['name'];
       $this->imageSize = $imageData['size'];
       $this->imageTemp = $imageData['tmp_name'];
       $this->imageError = $imageData['error'];
   }

   /**
    * Get Image Extention
    * @return Extention
    */
   public function getExtention()
   {
         //Get Image Extension
         $arrayName = explode(".",$this->imageName);
         $imageExtension = strtolower(end($arrayName));
         return $imageExtension ;
   }

    /**
    * Check If Found Errors
    * @return bool
    */
   public function validateImage()
   {
       if($this->imageError == 4)
       {
            $this->setErrors('You are Not Choosed File To uploaded.');
       }
       else
       {
            if(! in_array($this->getExtention(),$this->imageAllowedExtension))
            {
                $this->setErrors( "This Extension is Not <strong>Allowed</strong>");
            }
            else
            {
                if($this->imageError == 1)
                {
                    $this->setErrors('The file size exceeds the value specified.');
                }
                if($this->imageError == 2)
                {
                    $this->setErrors('The file size exceeds the value of the directive.');
                }
                if($this->imageError == 3)
                {
                    $this->setErrors('The file is not completely uploaded.');
                }
                if($this->imageError == 6)
                {
                    $this->setErrors('The temporary directory does not exist.');
                }
                if($this->imageSize > 736000)
                {
                    $this->setErrors("Image Can't Be Larger Than <strong>700 KB</strong>");
                }
            }
       } 
   }
   /**
    * generate Name
    * return New Name
    */
   public function generateName()
   {    
       $newName = md5(rand(0,1000000000000)).'_'.$this->imageName;
       return $newName;
   }

   /**
    * upload File
    * @return bool
    */
   public function uploadFile($directory)
   {
        $this->newImageName = $this->generateName();
        if (is_uploaded_file($this->imageTemp))
        { 
            move_uploaded_file($this->imageTemp, $directory."/".$this->newImageName);
            return true ;  
        }
        else
        { 
            $this ->setErrors("Something Went Wrong Please Try Again");
            return false ;
        }
   }

  /**
    * Set Errors
    * @param $error
    */
    public function setErrors($error)
    {
        $this ->imageErrors[] = $error;
    }

    /**
    * get Errors
    * @return $Array
    */
    public function getImageErrors()
    {
        return $this ->imageErrors;
    }
}