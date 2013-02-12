<?php
class Proposalgen_Model_Report_Step extends My_Model_Abstract
{
    const STEP_SURVEY_FINANCE       = 'finance';
    const STEP_SURVEY_PURCHASING    = 'purchasing';
    const STEP_SURVEY_IT            = 'it';
    const STEP_SURVEY_USERS         = 'users';
    const STEP_SURVEY_VERIFY        = 'verify';
    const STEP_FLEETDATA_UPLOAD     = 'upload';
    const STEP_FLEETDATA_MAPDEVICES = 'mapdevices';
    const STEP_FLEETDATA_SUMMARY    = 'summary';
    const STEP_REPORTSETTINGS       = 'reportsettings';
    const STEP_FINISHED             = 'finished';
    const GROUP_SURVEY              = 'Survey';
    const GROUP_FLEETDATA           = 'Fleet Data';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = array(
        self::STEP_SURVEY_FINANCE       => array(
            'name'       => 'Finance',
            'group'      => self::GROUP_SURVEY,
            'controller' => 'survey',
            'action'     => 'finance'
        ),
        self::STEP_SURVEY_PURCHASING    => array(
            'name'       => 'Purchasing',
            'group'      => self::GROUP_SURVEY,
            'controller' => 'survey',
            'action'     => 'purchasing'
        ),
        self::STEP_SURVEY_IT            => array(
            'name'       => 'IT',
            'group'      => self::GROUP_SURVEY,
            'controller' => 'survey',
            'action'     => 'it'
        ),
        self::STEP_SURVEY_USERS         => array(
            'name'       => 'Users',
            'group'      => self::GROUP_SURVEY,
            'controller' => 'survey',
            'action'     => 'users'
        ),
        self::STEP_SURVEY_VERIFY        => array(
            'name'       => 'Verify',
            'group'      => self::GROUP_SURVEY,
            'controller' => 'survey',
            'action'     => 'verify'
        ),
        self::STEP_FLEETDATA_UPLOAD     => array(
            'name'       => 'Upload',
            'group'      => self::GROUP_FLEETDATA,
            'controller' => 'fleet',
            'action'     => 'index'
        ),
        self::STEP_FLEETDATA_MAPDEVICES => array(
            'name'       => 'Map Devices',
            'group'      => self::GROUP_FLEETDATA,
            'controller' => 'fleet',
            'action'     => 'mapping'
        ),
        self::STEP_FLEETDATA_SUMMARY    => array(
            'name'       => 'Summary',
            'group'      => self::GROUP_FLEETDATA,
            'controller' => 'fleet',
            'action'     => 'summary'
        ),
        self::STEP_REPORTSETTINGS       => array(
            'name'       => 'Report Settings',
            'group'      => null,
            'controller' => 'fleet',
            'action'     => 'reportsettings'
        ),
        self::STEP_FINISHED             => array(
            'name'       => 'Reports',
            'group'      => null,
            'controller' => 'report_index',
            'action'     => 'index'
        )
    );

    /**
     * @var Proposalgen_Model_Report_Step[]
     */
    private static $steps;

    /**
     * Gets the report steps
     *
     * @return Proposalgen_Model_Report_Step[]
     */
    public static function getSteps ()
    {
        if (!isset(self::$steps))
        {
            $previousStep = null;
            $currentStep  = null;

            // Add Steps: WARNING, logic here will mess with your head.
            foreach (self::$stepNames as $stepName => $step)
            {
                // Move the old current step to be the previous step.
                $previousStep = $currentStep;

                // Create our new step
                $currentStep            = new Proposalgen_Model_Report_Step($step);
                $currentStep->enumValue = $stepName;

                // Set the previous step of our current step
                if ($previousStep instanceof Proposalgen_Model_Report_Step)
                {
                    $currentStep->previousStep = $previousStep;

                    // Add the current step as the last step's next step.
                    $previousStep->nextStep = $currentStep;
                }

                self::$steps [] = $currentStep;
            }
        }

        return self::$steps;
    }

    /**
     * The previous step in a proposal
     *
     * @var Proposalgen_Model_Report_Step
     */
    public $previousStep = null;

    /**
     * The next step in a proposal
     *
     * @var Proposalgen_Model_Report_Step
     */
    public $nextStep = null;

    /**
     * The name of the step
     *
     * @var string
     */
    public $name;

    /**
     * The group that the step is in
     *
     * @var string
     */
    public $group;

    /**
     * The controller that the step is on
     *
     * @var string
     */
    public $controller;

    /**
     * The action that the step is on
     *
     * @var string
     */
    public $action;

    /**
     * Is the step active?
     *
     * @var string
     */
    public $active = false;

    /**
     * Is the step active?
     *
     * @var string
     */
    public $canAccess = false;

    /**
     * The value of the step in the database
     *
     * @var string
     */
    public $enumValue;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->previousStep) && !is_null($params->previousStep))
        {
            $this->previousStep = $params->previousStep;
        }

        if (isset($params->nextStep) && !is_null($params->nextStep))
        {
            $this->nextStep = $params->nextStep;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->group) && !is_null($params->group))
        {
            $this->group = $params->group;
        }

        if (isset($params->controller) && !is_null($params->controller))
        {
            $this->controller = $params->controller;
        }

        if (isset($params->action) && !is_null($params->action))
        {
            $this->action = $params->action;
        }

        if (isset($params->active) && !is_null($params->active))
        {
            $this->active = $params->active;
        }

        if (isset($params->canAccess) && !is_null($params->canAccess))
        {
            $this->canAccess = $params->canAccess;
        }

        if (isset($params->enumValue) && !is_null($params->enumValue))
        {
            $this->enumValue = $params->enumValue;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "previousStep" => $this->previousStep,
            "nextStep"     => $this->nextStep,
            "name"         => $this->name,
            "group"        => $this->group,
            "controller"   => $this->controller,
            "action"       => $this->action,
            "active"       => $this->active,
            "canAccess"    => $this->canAccess,
            "enumValue"    => $this->enumValue,
        );
    }

    /**
     * Sets which steps are accessible
     *
     * @param array  $steps
     * @param string $stepName
     */
    public static function updateAccessibleSteps ($steps, $stepName)
    {
        $canAccess = true;
        /* @var $step Proposalgen_Model_Report_Step */
        foreach ($steps as $step)
        {
            $step->canAccess = $canAccess;

            if (strcasecmp($step->enumValue, $stepName) === 0)
            {
                $canAccess = false;
            }
        }
    }
}