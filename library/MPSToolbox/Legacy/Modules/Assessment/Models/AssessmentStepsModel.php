<?php

namespace MPSToolbox\Legacy\Modules\Assessment\Models;

use My_Navigation_Abstract;

/**
 * Class AssessmentStepsModel
 *
 * @package MPSToolbox\Legacy\Modules\Assessment\Models
 */
class AssessmentStepsModel extends My_Navigation_Abstract
{
    const STEP_FLEET_UPLOAD = 'upload';
    const STEP_SURVEY       = 'survey';
    const STEP_SETTINGS     = 'settings';
    const STEP_FINISHED     = 'finished';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = array(
        self::STEP_SURVEY       => array(
            'name'  => 'Survey',
            'route' => 'assessment.survey'
        ),
        self::STEP_SETTINGS     => array(
            'name'  => 'Settings',
            'route' => 'assessment.settings'
        ),
        self::STEP_FINISHED     => array(
            'name'  => 'Reports',
            'route' => 'assessment.report-index'
        )
    );

    /**
     * @var AssessmentStepsModel
     */
    private static $_instance;

    /**
     * Gets the assessment steps
     *
     * @return AssessmentStepsModel
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new AssessmentStepsModel();
        }

        return self::$_instance;
    }

    /**
     * Creates a new set of assessment steps
     */
    public function __construct ()
    {
        $this->title = "Assessment";
        $this->_setNewSteps(self::$stepNames);
    }


}