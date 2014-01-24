<?php

/**
 * Class Healthcheck_Model_Healthcheck_Steps
 */
class Healthcheck_Model_Healthcheck_Steps extends My_Navigation_Abstract
{
    const STEP_SETTINGS      = 'settings';
    const STEP_SELECT_UPLOAD = 'select-upload';
    const STEP_FINISHED      = 'finished';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = array(
        self::STEP_SELECT_UPLOAD => array(
            'module'     => 'healthcheck',
            'name'       => 'Select Upload',
            'controller' => 'index',
            'action'     => 'select-upload'
        ),
        self::STEP_SETTINGS      => array(
            'module'     => 'healthcheck',
            'name'       => 'Settings',
            'controller' => 'index',
            'action'     => 'settings'
        ),
        self::STEP_FINISHED      => array(
            'module'     => 'healthcheck',
            'name'       => 'Report',
            'controller' => 'report_index',
            'action'     => 'index'
        )
    );

    /**
     * @var Healthcheck_Model_Healthcheck_Steps
     */
    private static $_instance;

    /**
     * Gets the Health Check steps
     *
     * @return Healthcheck_Model_Healthcheck_Steps
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new Healthcheck_Model_Healthcheck_Steps();
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