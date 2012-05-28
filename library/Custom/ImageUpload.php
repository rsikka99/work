<?php
/**
 * ImageUplaod Class: This class contains common shared functions used when uploading and cropping images
 *
 * @author John Sadler
 */

class Custom_ImageUpload {
	
    public function __construct() {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		//only assign a new timestamp if the session variable is empty
		if(!isset($_SESSION['random_key']) || strlen($_SESSION['random_key']) == 0) {
		    $_SESSION['random_key'] = strtotime(date('Y-m-d H:i:s')); //assign the timestamp to the session variable
			$_SESSION['user_file_ext'] = "";
		}
		
		$upload_dir = $this->config->app->uploadPath; // The directory for the images to be saved in
		$upload_path = $upload_dir."/"; // The path to where the image will be saved
		$large_image_prefix = "resize_"; // The prefix name to large image
		$thumb_image_prefix = "thumbnail_"; // The prefix name to the thumb image
		$large_image_name = $large_image_prefix.$_SESSION['random_key']; // New name of the large image (append the timestamp to the filename)
		$thumb_image_name = $thumb_image_prefix.$_SESSION['random_key']; // New name of the thumbnail image (append the timestamp to the filename)
		$max_file = "1"; // Maximum file size in MB
		$max_width = "300"; // Max width allowed for the large image
		$thumb_width = "300"; // Width of thumbnail image
		$thumb_height = "200"; // Height of thumbnail image
		$current_large_image_width = null;
		$current_large_image_height = null;
		
		// Only one of these image types should be allowed for upload
		//$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
		$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg");
		$allowed_image_ext = array_unique($allowed_image_types); // do not change this
		$image_ext = ""; // initialise variable, do not change this.
		foreach($allowed_image_ext as $mime_type => $ext) {
		    $image_ext .= strtoupper($ext) . " ";
		}
		
		//using a session for scalability in the future... in case we decide to try to allow .gif or .png files
		//this may require another field for the file extension to be added to the database
		if(empty($_SESSION['user_file_ext'])) {
			$_SESSION['user_file_ext'] = '.jpg';
		}
		
		//Image Locations
		$large_image_location = $upload_path . $large_image_name . $_SESSION['user_file_ext'];
		$thumb_image_location = $upload_path . $thumb_image_name . $_SESSION['user_file_ext'];
    }
    
    public function rebuildImage($large_image_location, $thumb_image_location) {
		$dealer_companyTable = new Application_Model_DbTable_DealerCompany();
		$dealer_company = $dealer_companyTable->fetchRow('company_name = "MASTER"');
		
		//try to rebuild large image from database
		if(count($dealer_company) > 0) {
			$dealer_company_id = $dealer_company["dealer_company_id"];
		
			if(!file_exists($large_image_location) && !empty($dealer_company['full_company_logo'])) {
				$full_image = base64_decode($dealer_company['full_company_logo']);
				$full_image = imagecreatefromstring($full_image);
				imagejpeg($full_image, $large_image_location, 75);
				
				if(!file_exists($thumb_image_location) && !empty($dealer_company['company_logo'])) {
					$thumb_image = base64_decode($dealer_company['company_logo']);
					$thumb_image = imagecreatefromstring($thumb_image);
					imagejpeg($thumb_image, $thumb_image_location, 75);
				}
			}
		}
    }
    
	public function resizeImage($image,$width,$height,$scale) {
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}
		imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
		
		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage,$image); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$image,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$image);  
				break;
	    }
		
		chmod($image, 0777);
		return $image;
	}
	
	//You do not need to alter these functions
	public function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}
		imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage,$thumb_image_name); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$thumb_image_name,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$thumb_image_name);  
				break;
	    }
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}
	
	//You do not need to alter these functions
	public function getHeight($image) {
		$size = getimagesize($image);
		$height = $size[1];
		return $height;
	}
	
	//You do not need to alter these functions
	public function getWidth($image) {
		$size = getimagesize($image);
		$width = $size[0];
		return $width;
	}
	
}

?>