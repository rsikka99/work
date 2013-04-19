<?php
class Healthcheck_Model_Healthcheck_Steps extends My_Navigation_Abstract
{
    const STEP_REPORTSETTINGS   = 'Settings';
    const STEP_SELECTUPLOAD   = 'selectupload';
    const STEP_FINISHED         = 'finished';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = array(
        self::STEP_SELECTUPLOAD   => array(
            'module'     => 'healthcheck',
            'name'       => 'Select Upload',
            'group'      => null,
            'controller' => 'index',
            'action'     => 'index'
        ),
        self::STEP_REPORTSETTINGS   => array(
            'module'     => 'healthcheck',
            'name'       => 'Settings',
            'group'      => null,
            'controller' => 'index',
            'action'     => 'settings'
        ),
        self::STEP_FINISHED         => array(
            'module'     => 'healthcheck',
            'name'       => 'Report',
            'group'      => null,
            'controller' => 'index',
            'action'     => 'report'
        )
    );

    /**
     * @var Healthcheck_Model_Healthcheck_Steps
     */
    private static $_instance;

    /**
     * Gets the assessment steps
     *
     * @return Assessment_Model_Assessment_Steps
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