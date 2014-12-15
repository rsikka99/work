<?php

namespace Tangent;

class Functions
{

    /**
     * Retrieves a value in a range step table (array)
     *
     * @param mixed $match     The value we are comparing while iterating through the range
     * @param array $tableRows The range step table to iterate through
     * @param bool  $lessThan  whether or not we check for less than or greater than
     *
     * @return int
     */
    public static function getValueFromRangeStepTable ($match, $tableRows, $lessThan = true)
    {
        $result = 0;
        foreach ($tableRows as $value => $test)
        {
            $result = $value;
            if ($lessThan)
            {
                if ($match <= $test)
                {
                    break;
                }
            }
            else
            {
                if ($match >= $test)
                {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Sets the headers so that the browser will download the page
     *
     * @param string $fileName The filename that you want to use as a suggestion to the user.
     * @param string $mimeType Can be used to change the MIME type. You should probably leave this alone though. Defaults to "binary/octet-stream"
     */
    public static function setHeadersForDownload ($fileName, $mimeType = "binary/octet-stream")
    {
        header("Cache-Control: no-cache");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . urlencode($fileName));
        header("Content-Type: " . $mimeType);
        header("Content-Transfer-Encoding: binary");
    }

    /**
     * Forces the user to download the file specified and then exits
     *
     * @param string $fileName         The local path to the file you want the user to download
     * @param string $friendlyFileName The filename that you want to use as a suggestion to the user.
     *
     * @return bool False if the file did not exist
     */
    public static function forceDownloadFileAndExit ($fileName, $friendlyFileName = null)
    {
        $fileName = realpath($fileName);
        if (file_exists($fileName))
        {
            if (!$friendlyFileName)
            {
                $friendlyFileName = pathinfo($fileName, PATHINFO_FILENAME) . "." . pathinfo($fileName, PATHINFO_EXTENSION);
            }

            self::setHeadersForDownload($friendlyFileName);

            try
            {
                $fileSize = @filesize($fileName);
                if ($fileSize)
                {
                    header("Content-Length: " . $fileSize);
                    $fp = fopen($fileName, "r");
                    while (!feof($fp))
                    {
                        echo fread($fp, 65536);
                        flush(); // this is essential for large downloads
                    }
                    fclose($fp);
                    exit();
                }
            }
            catch (\Exception $e)
            {
                return false;
            }
        }

        return false;
    }

    /**
     * Takes a base64 encoded image and saves it to disk
     *
     * @param string $base64Image
     * @param string $savePath
     * @param int    $quality
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
     * Checks to see if a value is a date
     *
     * @param $value
     *
     * @return bool
     */
    public static function isDate ($value)
    {
        $pattern = '/^([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-5][0-9])\:([0-5][0-9])/';
        if (preg_match($pattern, $value))
        {
            return true;
        }
        else
        {
            return false;
        }

    }
}