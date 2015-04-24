<?php

namespace MPSToolbox\Legacy\Modules\Preferences\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\Preferences\Mappers\UserSettingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\SurveySettingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\SurveySettingModel;
use My_Model_Abstract;

/**
 * Class UserSettingModel
 *
 * @package MPSToolbox\Legacy\Modules\Preferences\Models
 */
class UserSettingModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $surveySettingId;

    /**
     * @var SurveySettingModel
     */
    protected $_surveySetting;

    /**
     * @var int
     */
    public $quoteSettingId;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }
        if (isset($params->surveySettingId) && !is_null($params->surveySettingId))
        {
            $this->surveySettingId = $params->surveySettingId;
        }
        if (isset($params->quoteSettingId) && !is_null($params->quoteSettingId))
        {
            $this->quoteSettingId = $params->quoteSettingId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "userId"              => $this->userId,
            "surveySettingId"     => $this->surveySettingId,
            "quoteSettingId"      => $this->quoteSettingId,
        ];
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
                $this->_surveySetting = new SurveySettingModel();
                SurveySettingMapper::getInstance()->insert($this->_surveySetting);
                $this->surveySettingId = $this->_surveySetting->id;

                // Save ourselves
                UserSettingMapper::getInstance()->save($this);
            }
        }

        return $this->_surveySetting;
    }
}