<?php
abstract class My_Controller_Report extends Tangent_Controller_Action
{
    /**
     * The Zend_Auth identity
     *
     * @var stdClass
     */
    protected $_identity;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * The full path that we are caching to
     *
     * @var string
     */
    protected $_fullCachePath;

    /**
     * The relative path to the cached object
     *
     * @var string
     */
    protected $_relativeCachePath;

    /**
     * @var string
     */
    protected $_firstStepName;

    /**
     * The navigation steps
     *
     * @var My_Navigation_Abstract
     */
    protected $_navigation;

    /**
     * Format details for csv files
     *
     * @var array
     */
    protected $_csvFormat = array(
        'extension'      => 'csv',
        'name'           => 'CSV',
        'loadingmessage' => '',
        'btnstyle'       => 'success'
    );

    /**
     * Format details for excel (xlsx) files
     *
     * @var array
     */
    protected $_excelFormat = array(
        'extension'      => 'xlsx',
        'name'           => 'Excel (XSLX)',
        'loadingmessage' => '',
        'btnstyle'       => 'success'
    );

    /**
     * Format details for pdf files
     *
     * @var array
     */
    protected $_pdfFormat = array(
        'extension'      => 'pdf',
        'name'           => 'PDF',
        'loadingmessage' => 'Please wait a moment while we generate your report',
        'btnstyle'       => 'danger'
    );

    /**
     * Format details for docx files
     *
     * @var array
     */
    protected $_wordFormat = array(
        'extension'      => 'docx',
        'name'           => 'Word (DOCX)',
        'loadingmessage' => 'Please wait a moment while we generate your report',
        'btnstyle'       => 'primary'
    );

    public function init ()
    {
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
    }

    /**
     * Downloads images ahead of time using curl.
     * Uses multi threading
     *
     * @param array   $imageArray An array of URLs to images. Currently only saves .png files
     * @param boolean $local      Whether or not the change the image path to a local path or a web accessible path
     *
     * @throws Exception
     * @return array
     */
    public function cachePNGImages ($imageArray, $local = true)
    {

        $cachePath       = $this->_fullCachePath;
        $publicCachePath = $this->_relativeCachePath;

        try
        {
            // Download files ahead of time
            $randomSalt         = strftime("%s") . mt_rand(10000, 30000);
            $imagePathAndPrefix = $cachePath . '/' . $randomSalt . "_";

            $newImages       = array();
            $curlHandle      = curl_multi_init();
            $curlConnections = array();
            $files           = array();

            foreach ($imageArray as $i => $fetchUrl)
            {
                $imageFilename = $imagePathAndPrefix . $i . '.png';
                if (file_exists($imageFilename)) // Delete file if it already exists
                {
                    unlink($imageFilename);
                }

                /**
                 * Google charts get generated in a weird way. We need to change &amp; to & in order for things to work properly.
                 */
                $fetchUrl             = str_replace("&amp;", "&", $fetchUrl);
                $fetchUrl             = str_replace(" ", "%20", $fetchUrl);
                $curlConnections [$i] = curl_init($fetchUrl);
                $files [$i]           = fopen($imageFilename, "w");

                curl_setopt($curlConnections [$i], CURLOPT_FILE, $files [$i]);
                curl_setopt($curlConnections [$i], CURLOPT_HEADER, 0);
                curl_setopt($curlConnections [$i], CURLOPT_CONNECTTIMEOUT, 60);
                curl_multi_add_handle($curlHandle, $curlConnections [$i]);
                $newImages [] = $imageFilename;
            }

            /**
             * Wait until all threads are finished downloading
             */
            do
            {
                curl_multi_exec($curlHandle, $active);
            } while ($active);

            /**
             * Update our image array to point to cached images
             */
            foreach ($imageArray as $i => & $imageUrl)
            {
                curl_multi_remove_handle($curlHandle, $curlConnections [$i]);
                curl_close($curlConnections [$i]);
                fclose($files [$i]);
                if ($local)
                {
                    $imageUrl = $cachePath . "/{$randomSalt}_{$i}.png";
                }
                else
                {
                    $imageUrl = $this->view->FullUrl($imageUrl = $publicCachePath . "/{$randomSalt}_{$i}.png");
                }

            }
            curl_multi_close($curlHandle);

            /**
             * Attempt to change permissions on our files
             */
            chmod($cachePath, 0777);
            foreach ($newImages as $filePath)
            {
                chmod($filePath, 0777);
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Could not cache image files!", 0, $e);
        }

        return $imageArray;

    }

    /**
     * Deletes old files in the report cache
     *
     * @throws Exception
     * @return int The number of files deleted
     */
    public function clearCacheForReport ()
    {
        $path = $this->_fullCachePath;
        try
        {
            $fileDeleteDate = strtotime("-1 hour");
            $files          = array();

            // Get all files to delete
            if (false !== ($handle = @opendir($path)))
            {
                // Get rid of cache to ensure we have proper information on the
                // files we want to delete.
                clearstatcache();
                while (false !== ($file = readdir($handle)))
                {
                    if ($file != "." && $file != "..")
                    {
                        // Only select the file if it is older than
                        // $fileDeleteDate
                        if (filemtime("$path/$file") < $fileDeleteDate)
                        {
                            $files [] = "$path/$file";
                        }
                    }
                }
                closedir($handle);
            }

            // Delete all files that we found
            foreach ($files as $file)
            {
                @unlink($file);
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error while cleaning the cache for the report");
        }

        return count($files);
    }


    /**
     * Gets the dealer logo file relative to the public folder
     *
     * @return string
     */
    public function getDealerLogoFile ()
    {
        $dealer   = Admin_Model_Mapper_Dealer::getInstance()->find($this->_identity->dealerId);
        $logoFile = false;
        if ($dealer)
        {
            if ($dealer->dealerLogoImageId > 0)
            {
                $logoFile = $dealer->getDealerLogoImageFile();
            }
        }


        if ($logoFile == false)
        {
            $logoFile = $this->view->theme("img/report_logo.png");
        }

        return $logoFile;
    }

    /**
     * Redirects the user to the very last available step
     *
     * @param string $stepName
     */
    public function redirectToLatestStep ($stepName)
    {
        $stage = ($stepName) ? : $this->_firstStepName;
        $this->_navigation->updateAccessibleSteps($stage);

        $firstStep  = false;
        $latestStep = false;
        foreach ($this->_navigation->steps as $step)
        {
            if ($firstStep === false)
            {
                $firstStep = $step;
            }

            if (!$step->canAccess)
            {
                break;
            }

            $latestStep = $step;
        }

        if ($latestStep)
        {
            $this->redirector($latestStep->action, $latestStep->controller, $latestStep->module);
        }
        else
        {
            $this->redirector($firstStep->action, $firstStep->controller, $firstStep->module);
        }
    }

    /**
     *  Generates a filename for a report
     *
     * @param $clientModel Quotegen_Model_Client
     * @param $reportName string
     *
     * @return string
     */
    public function generateReportFilename ($clientModel, $reportName)
    {
        $filter = new Tangent_Filter_Filename();
        $companyName = $filter->filter($clientModel->companyName);
        return $companyName . "_" . $reportName;
    }

}