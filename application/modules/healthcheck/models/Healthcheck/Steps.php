<?php
class Healthcheck_Model_Healthcheck_Steps extends My_Navigation_Abstract
{
    const STEP_REPORTSETTINGS = 'Settings';
    const STEP_SELECTUPLOAD   = 'selectupload';
    const STEP_FINISHED       = 'finished';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = array(
        self::STEP_SELECTUPLOAD   => array(
            'module'     => 'healthcheck',
            'name'       => 'Select Upload',
            'controller' => 'index',
            'action'     => 'selectupload'
        ),
        self::STEP_REPORTSETTINGS => array(
            'module'     => 'healthcheck',
            'name'       => 'Settings',
            'controller' => 'index',
            'action'     => 'settings'
        ),
        self::STEP_FINISHED       => array(
            'module'     => 'healthcheck',
            'name'       => 'Report',
            'controller' => 'report_healthcheck',
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

    public function __construct ()
    {
        $this->_setNewSteps(self::$stepNames);
    }
}