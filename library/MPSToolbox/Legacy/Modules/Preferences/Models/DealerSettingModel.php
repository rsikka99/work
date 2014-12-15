<?php

namespace MPSToolbox\Legacy\Modules\Preferences\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\Preferences\Mappers\DealerSettingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\SurveySettingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\SurveySettingModel;
use My_Model_Abstract;

/**
 * Class DealerSettingModel
 *
 * @package MPSToolbox\Legacy\Modules\Preferences\Models
 */
class DealerSettingModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $surveySettingId;

    /**
     * @var SurveySettingModel
     */
    protected $_surveySetting;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }
        if (isset($params->surveySettingId) && !is_null($params->surveySettingId))
        {
            $this->surveySettingId = $params->surveySettingId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "dealerId"            => $this->dealerId,
            "surveySettingId"     => $this->surveySettingId,
        );
    }



    /**
     * Gets the survey settings
     *
     * @return SurveySettingModel
     */
    public function getSurveySettings ()
    {
        if (!isset($this->_surveySetting))
        {
            $this->_surveySetting = SurveySettingMapper::getInstance()->find($this->surveySettingId);

            if (!$this->_surveySetting instanceof SurveySettingModel)
            {
                // Insert a new copy of the system setting
                $this->_surveySetting = SurveySettingMapper::getInstance()->fetchSystemSurveySettings();
                SurveySettingMapper::getInstance()->insert($this->_surveySetting);
                $this->surveySettingId = $this->_surveySetting->id;

                // Save ourselves
                DealerSettingMapper::getInstance()->save($this);
            }
        }

        return $this->_surveySetting;
    }
}