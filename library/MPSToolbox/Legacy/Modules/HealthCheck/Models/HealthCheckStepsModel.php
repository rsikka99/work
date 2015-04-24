<?php

namespace MPSToolbox\Legacy\Modules\HealthCheck\Models;

use My_Navigation_Abstract;

/**
 * Class MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckStepsModel
 */
class HealthCheckStepsModel extends My_Navigation_Abstract
{
    const STEP_SETTINGS      = 'settings';
    const STEP_SELECT_UPLOAD = 'select-upload';
    const STEP_FINISHED      = 'finished';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = [
        self::STEP_SETTINGS => [
            'name'  => 'Settings',
            'route' => 'healthcheck.settings'
        ],
        self::STEP_FINISHED => [
            'name'  => 'Report',
            'route' => 'healthcheck.report'
        ]
    ];

    /**
     * @var HealthCheckStepsModel
     */
    private static $_instance;

    /**
     * Gets the Health Check steps
     *
     * @return HealthCheckStepsModel
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new HealthCheckStepsModel();
        }

        return self::$_instance;
    }

    /**
     * Creates a new healthcheck step
     */
    public function __construct ()
    {
        $this->title = "Health Check";
        $this->_setNewSteps(self::$stepNames);
    }
}