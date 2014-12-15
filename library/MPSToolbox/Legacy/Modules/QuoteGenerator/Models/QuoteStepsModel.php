<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use My_Navigation_Abstract;

/**
 * Class AssessmentStepsModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class QuoteStepsModel extends My_Navigation_Abstract
{
    const STEP_ADD_HARDWARE  = 'add_hardware';
    const STEP_GROUP_DEVICES = 'group_devices';
    const STEP_ADD_PAGES     = 'pages';
    const STEP_FINANCING     = 'financing';
    const STEP_FINISHED      = 'finished';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = array(
        self::STEP_ADD_HARDWARE  => array(
            'name'  => 'Add Hardware',
            'route' => 'quotes.add-hardware'
        ),
        self::STEP_GROUP_DEVICES => array(
            'name'  => 'Group Devices',
            'route' => 'quotes.group-devices'
        ),
        self::STEP_ADD_PAGES     => array(
            'name'  => 'Pages',
            'route' => 'quotes.manage-pages'
        ),
        self::STEP_FINANCING     => array(
            'name'  => 'Hardware Financing',
            'route' => 'quotes.hardware-financing'
        ),
        self::STEP_FINISHED      => array(
            'name'  => 'Reports',
            'route' => 'quotes.reports'
        ),
    );

    /**
     * @var QuoteStepsModel
     */
    private static $_instance;

    /**
     * Gets the assessment steps
     *
     * @return QuoteStepsModel
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new QuoteStepsModel();
        }

        return self::$_instance;
    }

    /**
     * Creates a new set of assessment steps
     */
    public function __construct ()
    {
        $this->title = "Quote";
        $this->_setNewSteps(self::$stepNames);
    }


}