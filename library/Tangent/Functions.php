<?php

class Tangent_Functions
{

    /**
     * Retrieves a value in a range step table (array)
     * @param mixed $match The value we are comparing while iterating through the range
     * @param array $tableRows The range step table to iterate through
     * @param bool $lessThan whether or not we check for less than or greater than
     * @return The corresponding value in the range step table. Returns 0 by default
     */
    public static function getValueFromRangeStepTable ($match, $tableRows, $lessThan = true)
    {
        $result = 0;
        foreach ( $tableRows as $value => $test )
        {
            $result = $value;
            if ($lessThan)
            {
                if ($match <=$test)
                    break;
            }
            else
            {
                if ($match >= $test)
                    break;
            }
        }
        return $result;
    }

    /**
     * Sets the headers so that the browser will download the page
     * @param string $fileType The filename that you want to use as a suggestion to the user.
     * @param string $mimeType Can be used to change the MIME type. You should probably leave this alone though. Defaults to "binary/octet-stream"
     */
    public static function setHeadersForDownload ($fileName, $mimeType = "binary/octet-stream")
    {
        header("Cache-Control: no-cache");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . urlencode($fileName));
        header("Content-Type: binary/octet-stream");
        header("Content-Transfer-Encoding: binary");
    }

    /**
     * Forces the user to download the file specified and then exits
     * @param string $fileName The local path to the file you want the user to download
     * @param string $friendlyFileName The filename that you want to use as a suggestion to the user.
     * @return Returns false if the file did not exist
     */
    public static function forceDownloadFileAndExit ($fileName, $friendlyFileName = null)
    {
        $fileName = realpath($fileName);
        if (file_exists($fileName))
        {
            if (! $friendlyFileName)
                $friendlyFileName = pathinfo($fileName, PATHINFO_FILENAME) . "." . pathinfo($fileName, PATHINFO_EXTENSION);
            
            self::setHeadersForDownload($friendlyFileName);
            
            try
            {
                $fileSize = @filesize($fileName);
                if ($fileSize)
                {
                    header("Content-Length: " . $fileSize);
                    $fp = fopen($fileName, "r");
                    while ( ! feof($fp) )
                    {
                        echo fread($fp, 65536);
                        flush(); // this is essential for large downloads
                    }
                    fclose($fp);
                    exit();
                }
            }
            catch ( Exception $e )
            {
                return false;
            }
        }
        return false;
    }

    /**
     * Takes a base64 encoded image and saves it to disk
     * @param unknown_type $base64Image
     * @param unknown_type $savePath
     * @param unknown_type $quality
     */
    public static function createJPEGImageFromDatabaseData ($base64Image, $savePath, $quality = 85)
    {
        // Delete the file if it already exists
        if (file_exists($savePath))
        {
            unlink($savePath);
        }
        // Decode the image data
        $image = base64_decode($base64Image);
        // Make a GD image object
        $image = imagecreatefromstring($image);
        // Save the image object
        imagejpeg($image, $savePath, $quality);
    }
    
    /**
     *
     * Inserts rows of data into the database
     * @param 2D array $dataArray 2D array of rows of data to add to the database
     * @param array $columns Array of column names in the table
     * @param string $tableName Name of the table to add rows to.
     */
    public static function addArrayToDatabase ($dataArray, $columns, $tableName)
    {
    	$ctr = 1;
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$db->beginTransaction();
		try {
	        //Get database connection.
	        $db->getConnection();
	        //preparing the SQL statement
	    	$sql = "INSERT INTO ".$tableName. " (";
	    	//setting up the columns names, the last column name omits the comman
	    	foreach ($columns as $column){
	    		if ($ctr == count($columns))
	    			$sql .= $column;
	    		else
	    			$sql .= $column.",";
	    		$ctr++;
	    	}
	    	//adding the values
	    	$sql .= ") VALUES ";
	    	$outerCtr = 1;
	    	foreach ($dataArray as $dataRow){
	    		$sql .= "(";
	    		$ctr = 1;
	    		foreach ($dataRow as $value){
	    			//if the value is NOT numeric, need to add quotation marks around it
	    			//the last value omits the comma
	    			if (is_numeric($value) || Tangent_Functions::isDate($value))
	    			{
	    				if ($ctr == count($dataRow))
		    				$sql .= $value;
		    			else
		    				$sql .= $value.",";
	    			}
	    			else
	    			{
		    			if ($ctr == count($dataRow))
		    				$sql .= "'".$value."'";
		    			else
		    				$sql .= "'".$value."',";
	    			}
	    			$ctr++;
	    		}
	    		if ($outerCtr == count($dataArray))
	    			$sql .= ")";
	    		else
	    			$sql .= "),";
	    		$outerCtr++;
	    	}
	    	//executing the query
	        $stmt = $db->query($sql);
	        $db->commit();
		} catch(Zend_Db_Exception $e) {
			$db->rollback();
			return "Your file was not saved. Please double check the file and try again. If you continue to experience problems saving, contact your administrator.<br /><br />";
		} catch (Exception $e) {
			 $db->rollback();
			 return "Your file was not saved. Please double check the file and try again. If you continue to experience problems saving, contact your administrator.<br /><br />";
		}
		return null;
    }
    public static function isDate($value)
    {
    	$pattern  = '/^([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-5][0-9])\:([0-5][0-9])/';
    	if (preg_match ($pattern, $value))
    		return true;
    	else
    		return false;
    	
    }
}