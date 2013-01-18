<?php

abstract class My_Controller_Report extends Zend_Controller_Action
{
    /**
     * The current report
     *
     * @var Proposalgen_Model_Report
     */
    protected $Report;
    
    /**
     * The current proposal
     * 
     * @var Application_Model_Proposal_OfficeDepot
     */
    protected $Proposal;
    protected $csvFormat;
    protected $pdfFormat;
    protected $wordFormat;
    protected $ReportId;
    protected $ReportCompanyName;
    protected $ReportAbsoluteCachePath;
    protected $ReportCachePath;
    /**
     * User that is logged into the system.
     *
     * @var Application_Model_User
     */
    protected $_user;

    public function init ()
    {
        $session = new Zend_Session_Namespace('proposalgenerator_report');
        
        $this->ReportId = $session->reportId;
        $this->ReportAbsoluteCachePath = PUBLIC_PATH . "/cache/reports/" . $this->ReportId;
        $this->ReportCachePath = "/cache/reports/" . $this->ReportId;
        // Make the directory if it doesnt exist
        if (! is_dir($this->ReportAbsoluteCachePath))
        {
            if (! mkdir($this->ReportAbsoluteCachePath, 0777, true))
            {
                throw new Exception("Could not open cache folder! PATH:" . $this->ReportAbsoluteCachePath, 0);
            }
        }
        
        $this->view->ReportAbsoluteCachePath = $this->ReportAbsoluteCachePath;
        $this->view->ReportCachePath = $this->ReportCachePath;

        $this->_user = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);

        if ($this->ReportId < 1)
        {
            $this->_helper->flashMessenger(array (
                "error" => "Please select a report first." 
            ));
            // Send user to the index
            $this->_redirect('/');
        }
        
        $this->Report = Proposalgen_Model_Mapper_Report::getInstance()->find($this->ReportId);
        if ($this->Report === null)
        {
            $this->_helper->flashMessenger(array (
                "error" => "Please select a report first." 
            ));
            // Send user to the index
            $this->_redirect('/');
        }
        
        $this->view->reportMenu = new Custom_Report_Menu($this->Report);
        if (! $this->view->reportMenu->canAccessPage('finished'))
        {
            // Redirect to the last page that the user is allowed seeing
            $this->_redirect($this->view->reportMenu->currentPage());
        }
        
        // Setup the different file formats
        $this->csvFormat = (object)array (
            'extension' => 'csv', 
            'name' => 'Excel (CSV)', 
            'loadingmessage' => '', 
            'btnstyle' => 'success' 
        );
        $this->pdfFormat = (object)array (
            'extension' => 'pdf', 
            'name' => 'Adobe PDF', 
            'loadingmessage' => 'Please be patient as PDF\'s take a few minutes to generate', 
            'btnstyle' => 'danger' 
        );
        $this->wordFormat = (object)array (
            'extension' => 'docx', 
            'name' => 'Word (DOCX)', 
            'loadingmessage' => 'Please wait a moment while we generate your report', 
            'btnstyle' => 'primary' 
        );
    }

    /**
     * Gets the view ready to render a docx file
     */
    public function initDocx ()
    {
        require_once ('PHPWord.php');
        $this->view->phpword = new PHPWord();
    }

    /**
     * Gets the view ready to render a pdf file
     */
    public function initPdf ()
    {
    
    }

    /**
     * Gets the view ready to render the report
     */
    public function initReportVariables ($filename)
    {
        $this->view->publicFileName = $this->ReportCachePath . "/" . $filename;
        $this->view->savePath = $this->ReportAbsoluteCachePath . "/" . $filename;
        
        $this->view->proposal = $this->getProposal();
    }

    /**
     * Gets the proposal object for reports to use
     *
     * @throws Zend_Exception
     * @return Application_Model_Proposal_OfficeDepot
     */
    public function getProposal ()
    {
        if (! $this->Proposal)
        {
            $this->Proposal = false;
            $hasError = false;
            try
            {
                $this->Proposal = new Proposalgen_Model_Proposal_OfficeDepot($this->_user, $this->Report);
                
                if ($this->Report->devicesModified)
                {
                    $this->_redirect('/data/modificationwarning');
                }
                
                if (count($this->Proposal->DeviceCount) < 1)
                {
                    $this->view->ErrorMessages [] = "All uploaded printers were excluded from your report. Reports can not be generated until at least 1 printer is added.";
                    $hasError = true;
                }
            }
            catch ( Exception $e )
            {
                $this->view->ErrorMessages [] = "There was an error getting the reports.";
                throw new Zend_Exception("Error Getting Proposal Object.", 0, $e);
                $hasError = true;
            }
            
            if ($hasError)
            {
                $this->Proposal = false;
            }
        }
        return $this->Proposal;
    }

    /**
     * Deletes old files in the report cache
     *
     * @throws Exception
     * @return int The number of files deleted
     */
    public function clearCacheForReport ()
    {
        $path = $this->ReportAbsoluteCachePath;
        try
        {
            $fileDeleteDate = strtotime("-1 hour");
            $files = array ();
            
            // Get all files to delete
            if (false !== ($handle = @opendir($path)))
            {
                // Get rid of cache to ensure we have proper information on the
                // files we want to delete.
                clearstatcache();
                while ( false !== ($file = readdir($handle)) )
                {
                    if ($file != "." && $file != "..")
                    {
                        // Only select the file if it is older than
                        // $fileDeleteDate
                        if (filemtime("$path/$file") < $fileDeleteDate)
                            $files [] = "$path/$file";
                    }
                }
                closedir($handle);
            }
            
            // Delete all files that we found
            foreach ( $files as $file )
            {
                @unlink($file);
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Error while cleaning the cache for the report");
        }
        return count($files);
    }

    /**
     * Downloads images ahead of time using curl.
     * Uses multithreading
     *
     * @param $imageArray array
     *            An array of URL's to images. Currently only saves .png files
     * @param boolean $local
     *            Whether or not the change the image path to a local path or a
     *            web accessible path
     * @throws Exception
     * @return unknown
     */
    public function cachePNGImages ($imageArray, $local = true)
    {
        
        $cachePath = $this->ReportAbsoluteCachePath;
        
        $reportId = $this->ReportId;
        try
        {
            // Download files ahead of time
            $randomSalt = strftime("%s") . mt_rand(10000, 30000);
            $imagePathAndPrefix = $cachePath . '/' . $randomSalt . "_";
            
            $newImages = array ();
            $multihandle = curl_multi_init();
            
            foreach ( $imageArray as $i => $fetchUrl )
            {
                $imageFilename = $imagePathAndPrefix . $i . '.png';
                if (file_exists($imageFilename)) // Delete file if it already exists
                {
                    unlink($imageFilename);
                }
                
                // To fix the way the graphs are generated, we change &amp; to &
                $fetchUrl = str_replace("&amp;", "&", $fetchUrl);
                $fetchUrl = str_replace(" ", "%20", $fetchUrl);
                
                $conn [$i] = curl_init($fetchUrl);
                $file [$i] = fopen($imageFilename, "w");
                
                curl_setopt($conn [$i], CURLOPT_FILE, $file [$i]);
                curl_setopt($conn [$i], CURLOPT_HEADER, 0);
                curl_setopt($conn [$i], CURLOPT_CONNECTTIMEOUT, 60);
                curl_multi_add_handle($multihandle, $conn [$i]);
                $newImages [] = $imageFilename;
            }
            
            // Wait until all the images are downloaded
            do
            {
                $n = curl_multi_exec($multihandle, $active);
            }
            while ( $active );
            
            // Change the path of the images to a new path
            foreach ( $imageArray as $i => & $imageUrl )
            {
                curl_multi_remove_handle($multihandle, $conn [$i]);
                curl_close($conn [$i]);
                fclose($file [$i]);
                if ($local)
                {
                    $imageUrl = PUBLIC_PATH . "/cache/reports/$reportId/" . $randomSalt . "_$i.png";
                }
                else
                {
                    $imageUrl = $this->view->FullUrl("/cache/reports/$reportId/" . $randomSalt . "_$i.png");
                }
            
            }
            curl_multi_close($multihandle);
            // Change Permissions on all the images
            $newImages [] = $imageFilename;
            chmod($cachePath, 0777);
            foreach ( $newImages as $filePath )
            {
                chmod($filePath, 0777);
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Could not cache image files!", 0, $e);
        }
        return $imageArray;
    
    }

    /**
     * Gets the name of the company in the survey
     *
     * @throws Exception
     */
    public function getReportCompanyName ()
    {
        if (! isset($this->ReportCompanyName))
        {
            $questionTable = new Proposalgen_Model_DbTable_TextAnswer();
            $where = $questionTable->getAdapter()->quoteInto('report_id = ? AND question_id = 4', $this->ReportId, 'INTEGER');
            $row = $questionTable->fetchRow($where);
            
            if ($row ['textual_answer'])
            {
                $this->ReportCompanyName = $row ['textual_answer'];
            }
            else
            {
                throw new Exception("No Company Name Found!");
            }
        }
        return $this->ReportCompanyName;
    
    } // end function getReportCompanyName

    
    /**
     * Verifies that a replacement device of each type is found.
     */
    public function verifyReplacementDevices ()
    {
        $replacementDeviceMapper = Proposalgen_Model_Mapper_ReplacementDevice::getInstance();
        foreach ( Proposalgen_Model_ReplacementDevice::$replacementTypes as $type )
        {
            try
            {
                $row = null;
                $row = $replacementDeviceMapper->fetchRow(array (
                    'replacement_category = ?' => $type 
                ));
                if (! $row)
                {
                    throw new Exception("Error: Missing replacement device for the $type category.");
                }
            }
            catch ( Exception $e )
            {
                $this->view->ErrorMessages [] = "Error: Missing replacement device for the $type category.";
            }
        }
    
    }

} // end index controller

