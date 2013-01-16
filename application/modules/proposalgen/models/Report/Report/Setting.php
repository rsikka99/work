<?php
class Proposalgen_Model_Report_Report_Setting extends My_Model_Abstract
{
    /**
     * The report id
     *
     * @var int
     */
    public $reportId;

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

        if (isset($params->reportId) && !is_null($params->reportId))
        {
            $this->reportId = $params->reportId;
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
            "reportId"        => $this->reportId,
            "reportSettingId" => $this->reportSettingId,
        );
    }
}