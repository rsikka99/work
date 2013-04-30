<?php
/**
 * Class Hardwareoptimization_Model_Hardware_Optimization_Steps
 */
class Hardwareoptimization_Model_Hardware_Optimization_Steps extends My_Navigation_Abstract
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
    private static $stepNames = array(
        self::STEP_FLEET_UPLOAD => array(
            'name'       => 'Upload',
            'module'     => 'hardwareoptimization',
            'controller' => 'index',
            'action'     => 'select-upload'
        ),
        self::STEP_SETTINGS     => array(
            'name'       => 'Settings',
            'module'     => 'hardwareoptimization',
            'controller' => 'index',
            'action'     => 'settings'
        ),
        self::STEP_OPTIMIZE     => array(
            'name'       => 'Optimize',
            'module'     => 'hardwareoptimization',
            'controller' => 'index',
            'action'     => 'optimize'
        ),
        self::STEP_FINISHED     => array(
            'name'       => 'Reports',
            'module'     => 'hardwareoptimization',
            'controller' => 'report_index',
            'action'     => 'index'
        )
    );

    /**
     * @var Assessment_Model_Assessment_Steps
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
            self::$_instance = new Hardwareoptimization_Model_Hardware_Optimization_Steps();
        }

        return self::$_instance;
    }

    /**
     * Creates a new set of optimization steps
     */
    public function __construct ()
    {
        $this->_setNewSteps(self::$stepNames);
    }


}