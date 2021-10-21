<?php

namespace app\files_uploads;

/**
 * Class ImageUpload
 * @package app\files_uploads
 */

class ImageUpload
{
   private string $image_upload_name;
   private string $target_dir;
   private string $file_name;
   private string $file_type;
   private string $target_file;

   /**
    * Initialiaze the properties
    * @param array $image_upload
    * @param string $folder
    * @return void
    */

   public function __construct(array $image_upload, string $folder)
   {
      $this->image_upload_name = $image_upload['tmp_name'];
      $this->target_dir = dirname(__DIR__) . "/public/admin/uploads/$folder/";
      $this->file_name = $image_upload['name'];
      $this->file_type = $image_upload['type'];
      $this->target_file = $this->target_dir . basename($this->file_name);
   }

   /**
    * Upload a an image if there aren't errors
    * @return bool
    */
   public function upload()
   {
      return empty($this->checkErrors()) ? move_uploaded_file($this->image_upload_name, $this->target_file) : false;
   }

   /**
    * check if there are errors in the image
    * @return array
    */

   public function checkErrors()
   {
      $errors = [];

      if (!$this->checkImageFile($this->file_type)) {
         $errors['error_type'] = 'This is not an image';
      }

      return $errors;
   }

   /**
    * check if the image is in the correct format
    * @param string $type
    * @return bool
    */

   public function checkImageFile(string $type)
   {
      return preg_match('/image\/[a-z]+/i', $type) ? true : false;
   }
}
