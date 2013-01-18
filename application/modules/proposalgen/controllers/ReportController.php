<?php
class Proposalgen_ReportController extends My_Controller_Report
{

    function init ()
    {
        parent::init();
        // This is a list of reports that we can view.
        $this->view->availableReports = (object)array (
                "Assessment" => (object)array (
                        "pagetitle" => "Assessment", 
                        "active" => false, 
                        "url" => $this->view->baseUrl('/proposalgen/report/assessment')
                ), 
                "Solution" => (object)array (
                        "pagetitle" => "Solution", 
                        "active" => false, 
                        "url" => $this->view->baseUrl('/proposalgen/report/solution')
                ), 
                "GrossMargin" => (object)array (
                        "pagetitle" => "Gross Margin", 
                        "active" => false, 
                        "url" => $this->view->baseUrl('/proposalgen/report/grossmargin')
                ), 
                "PrintingDeviceList" => (object)array (
                        "pagetitle" => "Printing Device List", 
                        "active" => false, 
                        "url" => $this->view->baseUrl('/proposalgen/report/printingdevicelist')
                ) 
        );
    } // end init

    function preDispatch ()
    {
        $this->view->ErrorMessages = array ();
        // PDF Defaults
        $this->view->backleft = '';
        $this->view->backright = '';
        $this->view->backtop = '';
        $this->view->backbottom = '';
        $this->view->footer = '';
        $this->view->pagestyle = '';
        $this->view->titlePageStart = "";
        $this->view->pageStart = "";
        $this->view->pageEnd = "";
        $this->view->noBreakStart = "";
        $this->view->noBreakEnd = "";
        $this->view->orientation = 'P';
        $this->view->downloadPDF = false;
        $pdf = $this->_request->getParam('pdf', false);
        $this->view->downloadFileName = "Report.pdf";
        $this->view->isPDF = ($pdf !== FALSE) ? true : false;
    } // end preDispatch

    function postDispatch ()
    {

        $this->verifyReplacementDevices();
        // If we have error messages, send them to the error page
        if (count($this->view->ErrorMessages) > 0)
        {
            if (! isset($this->view->formTitle))
            {
                $this->view->formTitle = "Error";
            }
//            $layout = Zend_Registry::get('config')->interface->layout;
//            $this->view->layout()->setLayout($layout);
            $this->_helper->viewRenderer->setRender('report.error');
        }
        else
        {
            if ($this->view->isPDF)
            {
                $this->_helper->layout->setLayout('pdf');
                $this->view->isPDF = true;
                $this->view->noBreakStart = "<nobreak>";
                $this->view->noBreakEnd = "</nobreak>";
                $this->view->pageStart = "<page style='" . $this->view->pagestyle . "' backtop='" . $this->view->backtop . "' backbottom='" . $this->view->backbottom . "' backleft='" . $this->view->backleft . "' backright='" . $this->view->backright . "'>";
                // Footer can remain for future changes, such as logo or copyright etc
                $this->view->pageStart .= "<page_header>" . $this->view->header . "</page_header><page_footer>" . $this->view->footer . "</page_footer>";

                $this->view->pageEnd = "</page>";
                if ('download' === $this->_request->getParam('pdf'))
                {
                    $this->view->cachePath = "/cache/reports/" . $this->ReportId;
                    $this->view->downloadFileName = str_replace(array (
                            " ",
                            "/",
                            "\\",
                            ";",
                            "?",
                            "\"",
                            "'",
                            ",",
                            "%",
                            "&",
                            "#",
                            "@",
                            "!",
                            ">",
                            "<",
                            "+",
                            "=",
                            "{",
                            "}",
                            "[",
                            "]",
                            "|",
                            "~",
                            "`"
                    ), "_", $this->view->downloadFileName);
                    $this->view->downloadPDF = true;
                }
            }
        }
    }

    /**
     * * The default action
     */
    public function indexAction ()
    {
        $this->view->headScript()->prependFile($this->view->baseUrl("/js/htmlReport.js"));
        //$this->_helper->layout->setLayout('newreport');
        $this->view->formTitle = "Report Summary";
        //$this->view->availableReports->ReportIndex->active = true;
        

        // Clear the cache for the report before proceeding
        $this->clearCacheForReport();

        // proposal
        $proposal = $this->getProposal();
        $this->view->proposal = $proposal;
        $this->view->companyName = $this->getReportCompanyName(); // Set company
        // name
        $this->view->reportName = $this->getReportCompanyName();
    } // end indexAction

    
    /**
     * The assessmentAction displays the OD assessment report.
     * Data is retrieved
     * from the database and displayed using HTML, CSS, and javascript.
     */
    public function assessmentAction ()
    {
        $this->view->availableReports->Assessment->active = true;
        $this->view->formats = array (
                "/proposal/assessment/generate/format/docx" => $this->wordFormat
        );
        $this->view->reportTitle = "Assessment";
        
        $format = $this->_getParam("format", "html");
        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();

            $url = $this->view->serverUrl('/proposalgen/report/assessment');
            $this->view->url = $url;
            
            if (FALSE !== ($proposal = $this->getProposal()))
            {
                switch ($format)
                {
                    case "docx" :
                        // Add DOCX Logic here
                        break;
                    case "pdf" :
                        // Add PDF Logic here
                        $proposal->setGraphs($this->cachePNGImages($proposal->getGraphs()));
                        break;
                    case "html" :
                    default :
                        // Add HTML Logic here
                        break;
                }
                // If we're in a pdf, set cache the google chart images
                if ($this->view->isPDF)
                {
                    $proposal->setGraphs($this->cachePNGImages($proposal->getGraphs()));
                }
            }
            $this->view->proposal = $proposal;
        }
        catch ( Exception $e )
        {
            throw new Exception("Assessment could not be generated.", 0, $e);
        }
        
        if ($this->view->isPDF)
        {
            $this->view->downloadFileName = $this->getReportCompanyName() . "_assessment_" . date('Y_m_d') . ".pdf";
            // Set fonts, page margins and footer of PDF
            $this->view->backleft = 8;
            $this->view->backright = 8;
            $this->view->backtop = 5;
            $this->view->backbottom = 20;
            $this->view->footer = "<div class='center'><table cellspacing='0' class='footertable'><tr><td class='col1'></td><td class='col2'>[[page_cu]]</td><td class='col3'><img src='" . $this->view->serverUrl('/themes/' . $this->view->App()->theme . '/proposalgenerator/reports/images/officedepotLogo.jpg') . "'/></td></tr></table></div>";
            $this->view->pagestyle = "font-family: Arial; font-size: 11pt; line-height: 16pt;";
            $this->view->titlePageStart = "<page style='" . $this->view->pagestyle . "' backtop='" . $this->view->backtop . "' backbottom='" . $this->view->backbottom . "' backleft='" . $this->view->backleft . "' backright='" . $this->view->backright . "' backimg='" . $this->view->serverUrl('/themes/' . $this->view->App()->theme . '/proposalgenerator/reports/images/assessment.jpg') . "' backimgx='0'  backimgw='100%'>";
            $this->view->titlePageStart .= "<page_header>" . $this->view->header . "</page_header><page_footer>" . $this->view->footer . "</page_footer>";
        }
        else
        {
            $this->_helper->layout->setLayout('htmlreport');
        }
    } // End assessment action

    
    /**
     * The solution Action will be used to display the solution report
     * Data is grabbed from the database, and displayed using HTML, CSS, and
     * javascript.
     */
    public function solutionAction ()
    {
        $this->view->availableReports->Solution->active = true;
        
        $this->view->formats = array (
                "/proposal/solution/generate/format/docx" => $this->wordFormat 
        );
        
        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            
            $proposal = $this->getProposal();
            $this->view->proposal = $proposal;
            
            $url = $this->view->serverUrl();
            $this->view->url = $url;
        }
        catch ( Exception $e )
        {
            throw new Exception("Could not generate solution report.");
        }
        if ($this->view->isPDF)
        {
            $this->view->downloadFileName = $proposal->getReport()->CustomerCompanyName . "_solution_" . date('Y_m_d') . ".pdf";
            // Set fonts, page margins and footer of PDF
            $this->view->backleft = 8;
            $this->view->backright = 8;
            $this->view->backtop = 5;
            $this->view->backbottom = 20;
            $this->view->footer = "<div class='center'><table cellspacing='0' class='footertable'><tr><td class='col1'></td><td class='col2'>[[page_cu]]</td><td class='col3'><img src='" . $this->view->serverUrl('/themes/' . $this->view->App()->theme . '/proposalgenerator/reports/images/officedepotLogo.jpg') . "'/></td></tr></table></div>";
            $this->view->pagestyle = "font-family: Arial; font-size: 10pt; line-height: 16pt;";
            $this->view->titlePageStart = "<page style='" . $this->view->pagestyle . "' backtop='" . $this->view->backtop . "' backbottom='" . $this->view->backbottom . "' backleft='" . $this->view->backleft . "' backright='" . $this->view->backright . "' backimg='" . $this->view->serverUrl('/themes/' . $this->view->App()->theme . '/proposalgenerator/reports/images/solution.jpg') . "' backimgx='0'  backimgw='100%'>";
            $this->view->titlePageStart .= "<page_header>" . $this->view->header . "</page_header><page_footer>" . $this->view->footer . "</page_footer>";
        }
        else
        {
            $this->_helper->layout->setLayout('htmlreport');
        }
    } // end function solutionAction

    
    /**
     * The grossmargin Action will be used to display the grossmargin report
     */
    public function grossmarginAction ()
    {
        $this->view->availableReports->GrossMargin->active = true;
        $this->view->formats = array (
                "/proposal/grossmargin/generate/format/csv" => $this->csvFormat, 
                "/proposal/grossmargin/generate/format/docx" => $this->wordFormat 
        );
        
        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            
            $proposal = $this->getProposal();
            $this->view->proposal = $proposal;
            
            $url = $this->view->serverUrl();
            $this->view->url = $url;
        }
        catch ( Exception $e )
        {
            throw new Exception("Could not generate gross margin report.");
        }
        
        if ($this->view->isPDF)
        {
            $this->view->orientation = 'L';
            $this->view->downloadFileName = $this->getReportCompanyName() . "_grossmargin_" . date('Y_m_d') . ".pdf";
            // Set fonts, page margins and footer of PDF
            $this->view->backleft = 10;
            $this->view->backright = 10;
            $this->view->backtop = 5;
            $this->view->backbottom = 20;
            $this->view->footer = "<div class='center'><span>[[page_cu]]</span></div>";
            $this->view->pagestyle = "font-family: Arial; font-size: 10pt; line-height: 16pt;";
        }
        else
        {
            $this->_helper->layout->setLayout('htmlreport');
        }
    } // end function solutionAction

    public function printingdevicelistAction ()
    {
        $this->view->availableReports->PrintingDeviceList->active = true;
        $this->view->reportTitle = "Printing Device List";
        $this->view->formats = array (
                "/proposal/printingdevicelist/generate/format/csv" => $this->csvFormat, 
                "/proposal/printingdevicelist/generate/format/docx" => $this->wordFormat 
        );
        
        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            
            $proposal = $this->getProposal();
            $this->view->proposal = $proposal;
            
            $url = $this->view->serverUrl();
            $this->view->url = $url;
        }
        catch ( Exception $e )
        {
            throw new Exception("Could not generate printing device list report.");
        }
        
        $this->_helper->layout->setLayout('htmlreport');
    }

    /**
     * Shows specific details of a device or unknown device
     */
    public function devicedetailsAction ()
    {
        $device = null;
        $deviceId = $this->_request->getParam("id");
        $device = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceId);
        if (is_null($device))
        {
            $this->_redirect("/report/showdevices");
        }
        
        $report = $this->Report;
        $dealerCompany = Proposalgen_Model_DealerCompany::getCurrentUserCompany();
        $user = Proposalgen_Model_User::getCurrentUser();
        
        $proposal = new Proposalgen_Model_Proposal_OfficeDepot($user, $dealerCompany, $report);
        // In order to be able to get IT CPP we need all devices to be loaded
        $proposal->getDevices();
        
        $reportMargin = 1 - ((((int)$report->getReportPricingMargin())) / 100);
        $companyMargin = 1 - (((int)$dealerCompany->getDcPricingMargin()) / 100);
        
        Proposalgen_Model_DeviceInstance::processOverrides($device, $report, $reportMargin, $companyMargin);
        
        $this->view->device = $device;
        $this->_helper->layout->setLayout('blueprint');
    }

    /**
     * This action shows all the devices associated with a report
     */
    public function showdevicesAction ()
    {
        $device = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchAll(array (
                "report_id = ?" => $this->ReportId 
        ));
        $this->view->devices = $device;
        $this->_helper->layout->setLayout('blueprint');
    }
}