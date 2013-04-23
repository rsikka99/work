<?php
class Healthcheck_Library_Controller_Action extends Tangent_Controller_Action
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
     * @var Healthcheck_Model_Healthcheck
     */
    protected $_healthcheck;

    /**
     * The navigation steps
     *
     * @var Healthcheck_Model_Healthcheck_Steps
     */
    protected $_navigation;


    /**
     * An object containing various word styles
     *
     * @var stdClass
     */
    protected $_wordStyles;

    /**
     * Report name is the title behind the reports that are being generated.
     *
     * @var string
     */
    public $reportName;


    /**
     * The current proposal
     *
     * @var Healthcheck_ViewModel_Healthcheck
     */
    protected $_healthcheckViewModel;
    protected $_csvFormat;
    protected $_pdfFormat;
    protected $_wordFormat;
    protected $_reportId;
    protected $_reportCompanyName;
    protected $_reportAbsoluteCachePath;
    protected $_reportCachePath;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_navigation = Healthcheck_Model_Healthcheck_Steps::getInstance();

        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_mpsSession->selectedClientId);
            if (!$client instanceof Quotegen_Model_Client)
            {
                $this->_flashMessenger->addMessage(array(
                                                        "error" => "A client is not selected."
                                                   ));

                $this->redirector('index', 'index', 'index');
            }
        }

        $this->_reportAbsoluteCachePath = PUBLIC_PATH . "/cache/reports/healthcheck/" . $this->getHealthcheck()->id;
        $this->_reportCachePath         = "/cache/reports/healthcheck/" . $this->_healthcheck->id;

        // Make the directory if it doesn't exist
        if (!is_dir($this->_reportAbsoluteCachePath))
        {
            if (!mkdir($this->_reportAbsoluteCachePath, 0777, true))
            {
                throw new Exception("Could not open cache folder! PATH:" . $this->_reportAbsoluteCachePath, 0);
            }
        }

        $this->view->ReportAbsoluteCachePath = $this->_reportAbsoluteCachePath;
        $this->view->ReportCachePath         = $this->_reportCachePath;
    }

    public function initReportList ()
    {
        // This is a list of reports that we can view.
        $this->view->availableReports = (object)array(
            "Reports"              => (object)array(
                "pagetitle" => "Select a report...",
                "active"    => false,
                "url"       => $this->view->baseUrl('/healthcheck/report_healthcheck/index')
            ),
            "Healthcheck"           => (object)array(
                "pagetitle" => "Healthcheck",
                "active"    => false,
                "url"       => $this->view->baseUrl('/healthcheck/report_healthcheck/index')
            ),
        );
    }

    /**
     * Init function for html reports
     *
     * @throws Exception
     */
    public function initHtmlReport ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/htmlReport.js'));


        if ($this->getHealthcheck()->id < 1)
        {
            $this->_flashMessenger->addMessage(array("error" => "Please select a report first."));

            // Send user to the index
            $this->redirector('index', 'index', 'index');
        }

        // Setup the different file formats
        $this->_csvFormat           = (object)array(
            'extension'      => 'csv',
            'name'           => 'Excel (CSV)',
            'loadingmessage' => '',
            'btnstyle'       => 'success'
        );
        $this->_wordFormat          = (object)array(
            'extension'      => 'docx',
            'name'           => 'Word (DOCX)',
            'loadingmessage' => 'Please wait a moment while we generate your report',
            'btnstyle'       => 'primary'
        );
        $this->view->dealerLogoFile = $this->getDealerLogoFile();
    }

    /**
     * Prepares the view (for html reports) with the variables needed.
     *
     * @param $filename
     */
    public function initReportVariables ($filename)
    {
        $this->view->publicFileName = $this->_reportCachePath . "/" . $filename;
        $this->view->savePath       = $this->_reportAbsoluteCachePath . "/" . $filename;


        $this->view->dealerLogoFile = $this->getDealerLogoFile();

        $this->view->proposal = $this->getHealthcheckViewModel();
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
            $logoFile = $this->view->theme("proposalgenerator/reports/images/mpstoolbox_logo.jpg");
        }

        return $logoFile;
    }

    /**
     * Gets the proposal object for reports to use
     *
     * @throws Zend_Exception
     * @return Healthcheck_ViewModel_Healthcheck
     */
    public function getHealthcheckViewModel ()
    {
        if (!$this->_healthcheckViewModel)
        {
            $this->_healthcheckViewModel = false;
            $hasError                   = false;
            try
            {
                $this->_healthcheckViewModel = new Healthcheck_ViewModel_Healthcheck($this->getHealthcheck());

                if ($this->getHealthcheck()->devicesModified)
                {
                    $this->_redirect('/data/modificationwarning');
                }

                if ($this->_healthcheckViewModel->getDeviceCount() < 1)
                {
                    $this->view->ErrorMessages [] = "All uploaded printers were excluded from your report. Reports can not be generated until at least 1 printer is added.";
                    $hasError                     = true;
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages [] = "There was an error getting the reports.";
                throw new Zend_Exception("Error Getting Healthcheck View Model.", 0, $e);
            }

            if ($hasError)
            {
                $this->_healthcheckViewModel = false;
            }
        }

        return $this->_healthcheckViewModel;
    }


    /**
     * Deletes old files in the report cache
     *
     * @throws Exception
     * @return int The number of files deleted
     */
    public function clearCacheForReport ()
    {
        $path = $this->_reportAbsoluteCachePath;
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
     * Downloads images ahead of time using curl.
     * Uses multi threading
     *
     * @param         $imageArray array
     *                            An array of URL's to images. Currently only saves .png files
     * @param boolean $local
     *                            Whether or not the change the image path to a local path or a
     *                            web accessible path
     *
     * @throws Exception
     * @return array
     */
    public function cachePNGImages ($imageArray, $local = true)
    {

        $cachePath = $this->_reportAbsoluteCachePath;

        $reportId = $this->_reportId;
        try
        {
            // Download files ahead of time
            $randomSalt         = strftime("%s") . mt_rand(10000, 30000);
            $imagePathAndPrefix = $cachePath . '/' . $randomSalt . "_";

            $newImages   = array();
            $multihandle = curl_multi_init();

            foreach ($imageArray as $i => $fetchUrl)
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
            } while ($active);

            // Change the path of the images to a new path
            foreach ($imageArray as $i => & $imageUrl)
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
     * Gets the name of the company in the survey
     *
     * @throws Exception
     */
    public function getReportCompanyName ()
    {
        if (!isset($this->_reportCompanyName))
        {
            $questionTable = new Proposalgen_Model_DbTable_TextAnswer();
            $where         = $questionTable->getAdapter()->quoteInto('report_id = ? AND question_id = 4', $this->_reportId, 'INTEGER');
            $row           = $questionTable->fetchRow($where);

            if ($row ['textual_answer'])
            {
                $this->_reportCompanyName = $row ['textual_answer'];
            }
            else
            {
                throw new Exception("No Company Name Found!");
            }
        }

        return $this->_reportCompanyName;

    } // end function getReportCompanyName


    /**
     * Verifies that a replacement device of each type is found.
     */
    public function verifyReplacementDevices ()
    {
        $replacementDeviceMapper = Proposalgen_Model_Mapper_ReplacementDevice::getInstance();
        foreach (Proposalgen_Model_ReplacementDevice::$replacementTypes as $type)
        {
            try
            {
                $row = null;
                $row = $replacementDeviceMapper->fetchRow(array(
                                                               'replacement_category = ?' => $type
                                                          ));
                if (!$row)
                {
                    throw new Exception("Error: Missing replacement device for the $type category.");
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages [] = "Error: Missing replacement device for the $type category.";
            }
        }

    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        $stage = ($this->getHealthcheck()->stepName) ? : Healthcheck_Model_Healthcheck_Steps::STEP_SELECTUPLOAD;
        $this->_navigation->updateAccessibleSteps($stage);

        $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation->steps));
    }

    public function getWordStyles ()
    {
        if (!isset($this->_wordStyles))
        {
            // Get the for a dealer styles table
            $this->_wordStyles                                         = new stdClass();
            $this->_wordStyles->default->sectionHeaderFontColor        = "0096D6";
            $this->_wordStyles->default->sectionHeaderBorderColor      = "000000";
            $this->_wordStyles->default->subSectionBackgroundColor     = "0096D6";
            $this->_wordStyles->default->subSectionFontColor           = "FFFFFF";
            $this->_wordStyles->default->tableHeaderBackgroundColor    = "B8CCE3";
            $this->_wordStyles->default->tableSubHeaderBackgroundColor = "EAF0F7";
            $this->_wordStyles->default->tableHeaderFontColor          = "FFFFFF";
        }

        return $this->_wordStyles;
    }


    /**
     * Gets the healthcheck we're working on
     *
     * @return Healthcheck_Model_Healthcheck
     */
    public function getHealthcheck ()
    {
        if (!isset($this->_healthcheck))
        {
            if (isset($this->_mpsSession->healthcheckId) && $this->_mpsSession->healthcheckId > 0)
            {
                $this->_healthcheck = Healthcheck_Model_Mapper_Healthcheck::getInstance()->find($this->_mpsSession->healthcheckId);
            }
            else
            {
                $this->_healthcheck               = new Healthcheck_Model_Healthcheck();
                $this->_healthcheck->dateCreated  = date('Y-m-d H:i:s');
                $this->_healthcheck->lastModified = date('Y-m-d H:i:s');
                $this->_healthcheck->reportDate   = date('Y-m-d H:i:s');
                $this->_healthcheck->dealerId     = $this->_identity->dealerId;
                $this->_healthcheck->clientId     = $this->_mpsSession->selectedClientId;
            }
        }

        return $this->_healthcheck;
    }

    /**
     * Redirects the user to the very last available step
     */
    public function redirectToLatestStep ()
    {
        $stage = ($this->getHealthcheck()->stepName) ? : Healthcheck_Model_Healthcheck_Steps::STEP_SELECTUPLOAD;
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
}
