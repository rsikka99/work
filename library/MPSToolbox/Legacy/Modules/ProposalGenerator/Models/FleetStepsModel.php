<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use My_Navigation_Abstract;

/**
 * Class FleetStepsModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class FleetStepsModel extends My_Navigation_Abstract
{
    const STEP_FLEET_UPLOAD  = 'upload';
    const STEP_FLEET_MAPPING = 'mapping';
    const STEP_FLEET_SUMMARY = 'summary';

    /**
     * The order in which steps go.
     *
     * @var array
     */
    private static $stepNames = [
        self::STEP_FLEET_UPLOAD  => [
            'name'  => 'Upload',
            'route' => 'rms-upload',
        ],
        self::STEP_FLEET_MAPPING => [
            'name'  => 'Mapping',
            'route' => 'rms-upload.mapping',
        ],
        self::STEP_FLEET_SUMMARY => [
            'name'  => 'Summary',
            'route' => 'rms-upload.summary',
        ],
    ];

    /**
     * @var FleetStepsModel
     */
    private static $_instance;

    /**
     * Gets the fleet steps
     *
     * @return FleetStepsModel
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new FleetStepsModel();
        }

        return self::$_instance;
    }

    /**
     * Creates a new set of fleet steps
     */
    public function __construct ()
    {
        $this->title = "RMS Upload";
        $this->_setNewSteps(self::$stepNames);
    }
}