<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use My_Navigation_Abstract;

/**
 * Class HardwareOptimizationStepsModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class HardwareOptimizationStepsModel extends My_Navigation_Abstract
{
    const STEP_FLEET_UPLOAD = 'upload';
    const STEP_SETTINGS     = 'settings';
    const STEP_OPTIMIZE     = 'optimize';
    const STEP_FINISHED     = 'finished';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = [
        self::STEP_SETTINGS     => [
            'name'  => 'Settings',
            'route' => 'hardwareoptimization.settings',
        ],
        self::STEP_OPTIMIZE     => [
            'name'  => 'Optimize',
            'route' => 'hardwareoptimization.optimization',
        ],
        self::STEP_FINISHED     => [
            'name'  => 'Reports',
            'route' => 'hardwareoptimization.report-index',
        ],
    ];

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
            self::$_instance = new HardwareOptimizationStepsModel();
        }

        return self::$_instance;
    }

    /**
     * Creates a new set of optimization steps
     */
    public function __construct ()
    {
        $this->title = "Hardware Optimization";
        $this->_setNewSteps(self::$stepNames);
    }


}