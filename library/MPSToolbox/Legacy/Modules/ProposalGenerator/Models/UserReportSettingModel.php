<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class UserReportSettingModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class UserReportSettingModel extends My_Model_Abstract
{
    /**
     * The user id
     *
     * @var int
     */
    public $userId;

    /**
     * The setting id
     *
     * @var int
     */
    public $reportSettingId;

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

        if (isset($params->reportSettingId) && !is_null($params->reportSettingId))
        {
            $this->reportSettingId = $params->reportSettingId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "userId"          => $this->userId,
            "reportSettingId" => $this->reportSettingId,
        );
    }
}