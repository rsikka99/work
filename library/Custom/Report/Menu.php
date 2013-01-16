<?php

class Custom_Report_Menu
{
    private $report;
    
    // The database has an enum of the following:
    // 'company','general','finance','purchasing','it','users','verify','upload','mapping','leasing','settings','finished'
    /**
     * This is a list of items to be shown.
     * They MUST be in proper order
     *
     * @var array
     */
    private $reportMenuItems = array (
            'company' => array (
                    'url' => '/survey/company', 
                    'title' => 'Company' 
            ), 
            'general' => array (
                    'url' => '/survey/general', 
                    'title' => 'General' 
            ), 
            'finance' => array (
                    'url' => '/survey/finance', 
                    'title' => 'Finance' 
            ), 
            'purchasing' => array (
                    'url' => '/survey/purchasing', 
                    'title' => 'Purchasing' 
            ), 
            'it' => array (
                    'url' => '/survey/it', 
                    'title' => 'IT' 
            ), 
            'users' => array (
                    'url' => '/survey/users', 
                    'title' => 'Users' 
            ), 
            'verify' => array (
                    'url' => '/survey/verify', 
                    'title' => 'Verify' 
            ), 
            'upload' => array (
                    'url' => '/data', 
                    'title' => 'Upload' 
            ), 
            'mapping' => array (
                    'url' => '/data/devicemapping', 
                    'title' => 'Mapping' 
            ), 
            'leasing' => array (
                    'url' => '/data/deviceleasing', 
                    'title' => 'Summary' 
            ), 
            'settings' => array (
                    'url' => '/data/reportsettings', 
                    'title' => 'Settings' 
            ), 
            /*
             * 'disclaimer' => array (
                    'url' => '/report/acceptdisclaimer', 
                    'title' => 'Disclaimer' 
            ),
            */ 
            'finished' => array (
                    'url' => '/report', 
                    'title' => 'Report' 
            ) 
    );

    /**
     *
     * @param $report Proposalgen_Model_Report           
     */
    public function __construct ($report)
    {
        $this->report = $report;
    }

    /**
     * Returns the current page for the report.
     *
     * @return string
     */
    public function currentPage ()
    {
        if (is_null($this->report->reportStage))
        {
            $this->reportMenuItems ['company'] ['url'];
        }
        else
        {
            return $this->reportMenuItems [$this->report->reportStage] ['url'];
        }
    }

    /**
     * Determines whether or not we are allowed to access the currentstage
     *
     * @param $currentStage string
     * @return boolean
     */
    public function canAccessPage ($currentStage)
    {
        $isAllowed = false;
        if (! is_null($this->report->reportStage))
        {
            foreach ( $this->reportMenuItems as $stage => $menuItem )
            {
                if ($currentStage === $stage)
                {
                    $isAllowed = true;
                }
                if ($stage === $this->report->reportStage)
                {
                    break;
                }
            }
        }
        return $isAllowed;
    }

    /**
     * This function renders an html list of the current accessible pages
     *
     * @return string
     */
    public function render ($stage = null)
    {
        $menu = "";
        
        if (is_null($stage))
        {
            $stage = $this->report->reportStage;
        }
        
        if (! is_null($stage))
        {
            $menu = "<ul>";
            $baseUrl = new Zend_View_Helper_BaseUrl();
            foreach ( $this->reportMenuItems as $stage => $menuItem )
            {
                $menu .= "<li><a href='" . $baseUrl->baseUrl($menuItem ['url']) . "'>$menuItem[title]</a></li>";
                if ($stage === $this->report->reportStage)
                {
                    break;
                }
            }
            $menu .= "</ul>";
        }
        return $menu;
    }
}

?>