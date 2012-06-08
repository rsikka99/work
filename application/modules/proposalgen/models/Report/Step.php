<?php

/**
 * Class Proposalgen_Model_Report_Step
 */
class Proposalgen_Model_Report_Step extends My_Model_Abstract
{
    const STEP_SURVEY_COMPANY = 'company';
    const STEP_SURVEY_GENERAL = 'general';
    const STEP_SURVEY_FINANCE = 'finance';
    const STEP_SURVEY_PURCHASING = 'purchasing';
    const STEP_SURVEY_IT = 'it';
    const STEP_SURVEY_USERS = 'users';
    const STEP_SURVEY_VERIFY = 'verify';
    const STEP_FLEETDATA_UPLOAD = 'upload';
    const STEP_FLEETDATA_MAPDEVICES = 'mapdevices';
    const STEP_FLEETDATA_SUMMARY = 'summary';
    const STEP_REPORTSETTINGS = 'reportsettings';
    const STEP_FINISHED = 'finished';
    const GROUP_SURVEY = 'Survey';
    const GROUP_FLEETDATA = 'Fleet Data';
    
    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = array (
            self::STEP_SURVEY_COMPANY => array (
                    'name' => 'Company', 
                    'group' => self::GROUP_SURVEY, 
                    'controller' => 'survey', 
                    'action' => 'company', 
                    'canAccess' => true 
            ), 
            self::STEP_SURVEY_GENERAL => array (
                    'name' => 'General', 
                    'group' => self::GROUP_SURVEY, 
                    'controller' => 'survey', 
                    'action' => 'general' 
            ), 
            self::STEP_SURVEY_FINANCE => array (
                    'name' => 'Finance', 
                    'group' => self::GROUP_SURVEY, 
                    'controller' => 'survey', 
                    'action' => 'finance' 
            ), 
            self::STEP_SURVEY_PURCHASING => array (
                    'name' => 'Purchasing', 
                    'group' => self::GROUP_SURVEY, 
                    'controller' => 'survey', 
                    'action' => 'purchasing' 
            ), 
            self::STEP_SURVEY_IT => array (
                    'name' => 'IT', 
                    'group' => self::GROUP_SURVEY, 
                    'controller' => 'survey', 
                    'action' => 'it' 
            ), 
            self::STEP_SURVEY_USERS => array (
                    'name' => 'Users', 
                    'group' => self::GROUP_SURVEY, 
                    'controller' => 'survey', 
                    'action' => 'users' 
            ), 
            self::STEP_SURVEY_VERIFY => array (
                    'name' => 'Verify', 
                    'group' => self::GROUP_SURVEY, 
                    'controller' => 'survey', 
                    'action' => 'verify' 
            ), 
            self::STEP_FLEETDATA_UPLOAD => array (
                    'name' => 'Upload', 
                    'group' => self::GROUP_FLEETDATA, 
                    'controller' => 'fleet', 
                    'action' => 'index' 
            ), 
            self::STEP_FLEETDATA_MAPDEVICES => array (
                    'name' => 'Map Devices', 
                    'group' => self::GROUP_FLEETDATA, 
                    'controller' => 'fleet', 
                    'action' => 'devicemapping' 
            ), 
            self::STEP_FLEETDATA_SUMMARY => array (
                    'name' => 'Summary', 
                    'group' => self::GROUP_FLEETDATA, 
                    'controller' => 'fleet', 
                    'action' => 'deviceleasing' 
            ), 
            self::STEP_REPORTSETTINGS => array (
                    'name' => 'Report Settings', 
                    'group' => null, 
                    'controller' => 'fleet', 
                    'action' => 'reportsettings' 
            ), 
            self::STEP_FINISHED => array (
                    'name' => 'Reports', 
                    'group' => null, 
                    'controller' => 'report', 
                    'action' => 'index' 
            ) 
    );
    private static $steps;

    public static function getSteps ()
    {
        if (! isset(self::$steps))
        {
            $previousStep = null;
            $currentStep = null;
            
            // Add Steps: WARNING, logic here will mess with your head.
            foreach ( self::$stepNames as $stepName => $step )
            {
                // Move the old current step to be the previous step.
                $previousStep = $currentStep;
                
                // Create our new step
                $currentStep = new Proposalgen_Model_Report_Step($step);
                $currentStep->setEnumValue($stepName);
                
                // Set the previous step of our current step
                if ($previousStep !== null)
                {
                    $currentStep->setPreviousStep($previousStep);
                    
                    // Add the current step as the previous's next step.
                    $previousStep->setNextStep($currentStep);
                }
                
                self::$steps [] = $currentStep;
            }
        }
        return self::$steps;
    }
    
    /**
     * The previous step in a proposal
     *
     * @var Proposalgen_Model_Proposal_Step
     */
    protected $_previousStep = null;
    
    /**
     * The next step in a proposal
     *
     * @var Proposalgen_Model_Proposal_Step
     */
    protected $_nextStep = null;
    
    /**
     * The name of the step
     *
     * @var String
     */
    protected $_name;
    
    /**
     * The group that the step is in
     *
     * @var String
     */
    protected $_group;
    
    /**
     * The controller that the step is on
     *
     * @var string
     */
    protected $_controller;
    
    /**
     * The action that the step is on
     *
     * @var string
     */
    protected $_action;
    
    /**
     * Is the step active?
     *
     * @var string
     */
    protected $_active = false;
    
    /**
     * Is the step active?
     *
     * @var string
     */
    protected $_canAccess = false;
    
    /**
     * The value of the step in the database
     *
     * @var string
     */
    protected $_enumValue;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        // Convert the array into an object
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        
        // Set the fields if they were passed in
        if (isset($params->previousStep))
            $this->setPreviousStep($params->previousStep);
        if (isset($params->nextStep))
            $this->setNextStep($params->nextStep);
        if (isset($params->name))
            $this->setName($params->name);
        if (isset($params->group))
            $this->setGroup($params->group);
        if (isset($params->controller))
            $this->setController($params->controller);
        if (isset($params->action))
            $this->setAction($params->action);
        if (isset($params->active))
            $this->setActive($params->active);
        if (isset($params->canAccess))
            $this->setCanAccess($params->canAccess);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                "previousStep" => $this->getPreviousStep(), 
                "nextStep" => $this->getNextStep(), 
                "name" => $this->getName(), 
                "group" => $this->getGroup(), 
                "controller" => $this->getController(), 
                "action" => $this->getAction(), 
                "active" => $this->getActive(), 
                "canAccess" => $this->getCanAccess() 
        );
    }

    /**
     * Sets which steps are accessible
     *
     * @param array $steps            
     * @param string $stepName            
     */
    public static function updateAccessibleSteps ($steps, $stepName)
    {
        $canAccess = true;
        /* @var $step Proposalgen_Model_Report_Step */
        foreach ( $steps as $step )
        {
            $step->setCanAccess($canAccess);
            
            if (strcasecmp($step->getName(), $stepName) === 0)
            {
                $canAccess = false;
            }
        }
    }

    /**
     *
     * @return Proposalgen_Model_Report_Step
     */
    public function getPreviousStep ()
    {
        return $this->_previousStep;
    }

    /**
     *
     * @param Proposalgen_Model_Proposal_Step $_previousStep            
     */
    public function setPreviousStep ($_previousStep)
    {
        $this->_previousStep = $_previousStep;
        return $this;
    }

    /**
     *
     * @return Proposalgen_Model_Report_Step
     */
    public function getNextStep ()
    {
        return $this->_nextStep;
    }

    /**
     *
     * @param Proposalgen_Model_Proposal_Step $_nextStep            
     */
    public function setNextStep ($_nextStep)
    {
        $this->_nextStep = $_nextStep;
        return $this;
    }

    /**
     *
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     *
     * @param string $_name            
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

    /**
     *
     * @return the $_group
     */
    public function getGroup ()
    {
        return $this->_group;
    }

    /**
     *
     * @param string $_group            
     */
    public function setGroup ($_group)
    {
        $this->_group = $_group;
        return $this;
    }

    /**
     *
     * @return the $_controller
     */
    public function getController ()
    {
        return $this->_controller;
    }

    /**
     *
     * @param string $_controller            
     */
    public function setController ($_controller)
    {
        $this->_controller = $_controller;
        return $this;
    }

    /**
     *
     * @return the $_action
     */
    public function getAction ()
    {
        return $this->_action;
    }

    /**
     *
     * @param string $_action            
     */
    public function setAction ($_action)
    {
        $this->_action = $_action;
        return $this;
    }

    /**
     *
     * @return the $_active
     */
    public function getActive ()
    {
        return $this->_active;
    }

    /**
     *
     * @param string $_active            
     */
    public function setActive ($_active)
    {
        $this->_active = $_active;
        return $this;
    }

    /**
     *
     * @return the $_canAccess
     */
    public function getCanAccess ()
    {
        return $this->_canAccess;
    }

    /**
     *
     * @param string $_canAccess            
     */
    public function setCanAccess ($_canAccess)
    {
        $this->_canAccess = $_canAccess;
        return $this;
    }

    /**
     *
     * @return the $_enumValue
     */
    public function getEnumValue ()
    {
        return $this->_enumValue;
    }

    /**
     *
     * @param string $_enumValue            
     */
    public function setEnumValue ($_enumValue)
    {
        $this->_enumValue = $_enumValue;
        return $this;
    }
}