<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class SurveySettingModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class SurveySettingModel extends My_Model_Abstract
{
    /**
     * The id of the setting
     *
     * @var int
     */
    public $id;

    /**
     * The monochrome page coverage
     *
     * @var int
     */
    public $pageCoverageMono;

    /**
     * The color page coverage
     *
     * @var int
     */
    public $pageCoverageColor;

    /**
     * Overrides all the settings.
     * Null values will be excluded.
     *
     * @param SurveySettingModel|array $settings These can be either a MPSToolbox\Legacy\Modules\ProposalGenerator\Models\SurveySettingModel or an array of settings
     */
    public function ApplyOverride ($settings)
    {
        if ($settings instanceof SurveySettingModel)
        {
            $settings = $settings->toArray();
        }

        $this->populate($settings);
    }

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->pageCoverageMono) && !is_null($params->pageCoverageMono))
        {
            $this->pageCoverageMono = $params->pageCoverageMono;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"                => $this->id,
            "pageCoverageMono"  => $this->pageCoverageMono,
            "pageCoverageColor" => $this->pageCoverageColor,
        ];
    }
}