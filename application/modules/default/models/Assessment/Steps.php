<?php
class Assessment_Model_Assessment_Steps extends My_Navigation_Abstract
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
        self::STEP_FLEET_UPLOAD => array(
            'name'       => 'Upload',
            'module'     => 'assessment',
            'controller' => 'index',
            'action'     => 'index'
        ),
        self::STEP_SURVEY       => array(
            'name'       => 'Survey',
            'module'     => 'assessment',
            'controller' => 'index',
            'action'     => 'survey'
        ),
        self::STEP_SETTINGS     => array(
            'name'       => 'Settings',
            'module'     => 'assessment',
            'controller' => 'index',
            'action'     => 'settings'
        ),
        self::STEP_FINISHED     => array(
            'name'       => 'Reports',
            'module'     => 'assessment',
            'controller' => 'index',
            'action'     => 'report'
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
            self::$_instance = new Assessment_Model_Assessment_Steps();
        }

        return self::$_instance;
    }

    public function __construct ()
    {
        $this->_setNewSteps(self::$stepNames);
    }


}