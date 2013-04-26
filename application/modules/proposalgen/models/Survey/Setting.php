<?php
class Proposalgen_Model_Survey_Setting extends My_Model_Abstract
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
     * @param Proposalgen_Model_Survey_Setting|array $settings These can be either a Proposalgen_Model_Survey_Setting or an array of settings
     */
    public function ApplyOverride ($settings)
    {
        if ($settings instanceof Proposalgen_Model_Survey_Setting)
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
        return array(
            "id"                => $this->id,
            "pageCoverageMono"  => $this->pageCoverageMono,
            "pageCoverageColor" => $this->pageCoverageColor,
        );
    }
}