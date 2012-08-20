<?php

class Proposalgen_Service_ReportSettings
{
    /**
     * The form for a client
     *
     * @var Proposalgen_Form_Settings_Report
     */
    protected $_form;
    
    /**
     * The system report settings
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_systemSettings;
    
    /**
     * The user report settings
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_userSettings;
    
    /**
     * The report's report settings
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_reportSettings;
    
    /**
     * The default settings (uses overrides)
     *
     * @var Proposalgen_Model_Report_Setting
     */
    protected $_defaultSettings;
    
    /**
     * The report
     *
     * @var Proposalgen_Model_Report
     */
    protected $_report;

    public function __construct ($reportId, $userId)
    {
        $this->_report = Proposalgen_Model_Mapper_Report::getInstance()->find($reportId);
        $this->_systemSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchSystemReportSetting();
        $this->_userSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchUserReportSetting($userId);
        $this->_reportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchReportReportSetting($reportId);
        
        // Calculate the default settings
        $this->_defaultSettings = new Proposalgen_Model_Report_Setting($this->_systemSettings->toArray());
        $this->_defaultSettings->populate($this->_userSettings->toArray());
    }

    /**
     * Gets the client form
     *
     * @return Proposalgen_Form_Settings_Report
     */
    public function getForm ()
    {
        if (! isset($this->_form))
        {
            $this->_form = new Proposalgen_Form_Settings_Report($this->_defaultSettings);
            
            // Populate with initial data?
            $this->_form->populate($this->_reportSettings->toArray());
            $reportDate = date('m/d/Y', strtotime($this->_report->getReportDate()));
            $this->_form->populate(array (
                    'reportDate' => $reportDate 
            ));
            
            // FIXME: This shouldn't be here
            $this->_form->setDecorators(array (
                    array (
                            'ViewScript', 
                            array (
                                    'viewScript' => 'forms/settings/report.phtml' 
                            ) 
                    ) 
            ));
        }
        return $this->_form;
    }

    /**
     * Validates the data with the form
     *
     * @param array $data
     *            The array of data to validate
     * @return array The array of valid and filtered data, or false on error.
     */
    protected function validateAndFilterData ($data)
    {
        $validData = false;
        $form = $this->getForm();
        
        if ($form->isValid($data))
        {
            $validData = $form->getValues();
        }
        else
        {
            $this->getForm()->buildBootstrapErrorDecorators();
        }
        return $validData;
    }

    /**
     * Updates the report's settings
     *
     * @param array $data            
     * @return boolean
     */
    public function update ($data)
    {
        $validData = $this->validateAndFilterData($data);
        if ($validData)
        {
            $reportDate = date('Y-m-d h:i:s', strtotime($validData ['reportDate']));
            $this->_report->setReportDate($reportDate);
            Proposalgen_Model_Mapper_Report::getInstance()->save($this->_report);
            
            foreach ( $validData as $key => $value )
            {
                if (empty($value))
                {
                    unset($validData [$key]);
                }
            }
            // Save the id as it will get erased
            $reportSettingsId = $this->_reportSettings->getId();
            
            $this->_reportSettings->populate($this->_defaultSettings->toArray());
            $this->_reportSettings->populate($validData);
            
            // Restore the ID
            $this->_reportSettings->setId($reportSettingsId);
            
            Proposalgen_Model_Mapper_Report_Setting::getInstance()->save($this->_reportSettings);
            $this->getForm()->populate($this->_reportSettings->toArray());
            
            return true;
        }
        return false;
    }
}

?>