<?php

/**
 * Class Proposalgen_Model_Fleet_Steps
 */
class Proposalgen_Model_Fleet_Steps extends My_Navigation_Abstract
{
    const STEP_FLEET_UPLOAD  = 'upload';
    const STEP_FLEET_MAPPING = 'mapping';
    const STEP_FLEET_SUMMARY = 'summary';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = array(
        self::STEP_FLEET_UPLOAD  => array(
            'name'       => 'Upload',
            'module'     => 'proposalgen',
            'controller' => 'fleet',
            'action'     => 'index'
        ),
        self::STEP_FLEET_MAPPING => array(
            'name'       => 'Mapping',
            'module'     => 'proposalgen',
            'controller' => 'fleet',
            'action'     => 'mapping'
        ),
        self::STEP_FLEET_SUMMARY => array(
            'name'       => 'Summary',
            'module'     => 'proposalgen',
            'controller' => 'fleet',
            'action'     => 'summary'
        )
    );

    /**
     * @var Proposalgen_Model_Fleet_Steps
     */
    private static $_instance;

    /**
     * Gets the fleet steps
     *
     * @return Proposalgen_Model_Fleet_Steps
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new Proposalgen_Model_Fleet_Steps();
        }

        return self::$_instance;
    }

    /**
     * Creates a new set of fleet steps
     */
    public function __construct ()
    {
        $this->_setNewSteps(self::$stepNames);
    }


}