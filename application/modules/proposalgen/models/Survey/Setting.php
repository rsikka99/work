<?php

/**
 * Class Proposalgen_Model_Survey_Setting
 */
class Proposalgen_Model_Survey_Setting extends My_Model_Abstract
{
    /**
     * The id of the setting
     *
     * @var int
     */
    protected $_id;
    
    /**
     * The monochrome page coverage
     *
     * @var int
     */
    protected $_pageCoverageMono;
    
    /**
     * The color page coverage
     *
     * @var int
     */
    protected $_pageCoverageColor;

    /**
     * Overrides all the settings.
     * Null values will be excluded.
     *
     * @param Proposalgen_Model_Survey_Setting $settings
     *            These can be either a Proposalgen_Model_Survey_Setting or an array of settings
     */
    public function ApplyOverride ($settings)
    {
        if ($settings instanceof Proposalgen_Model_Survey_Setting)
        {
            $settings = $settings->toArray();
        }
        
        $this->populate($settings);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        // Convert the array into an object
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        
        // Set the fields if they were passed in
        if (isset($params->id))
            $this->setId($params->id);
        if (isset($params->pageCoverageMono))
            $this->setPageCoverageMono($params->pageCoverageMono);
        if (isset($params->pageCoverageColor))
            $this->setPageCoverageColor($params->pageCoverageColor);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                "page_coverage_mono" => $this->getPageCoverageColor(),
                "page_coverage_color" => $this->getPageCoverageMono()
        );
    }

    /**
     * Gets the id of the survey setting
     *
     * @return number
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the survey setting
     *
     * @param number $_id
     *            The new id to set
     * @return \Proposalgen_Model_Survey_Setting
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
        return $this;
    }

    /**
     * Gets the monochrome page coverage of the survey setting
     *
     * @return number
     */
    public function getPageCoverageMono ()
    {
        return $this->_pageCoverageMono;
    }

    /**
     * Sets the monochrome page coverage of the survey setting
     *
     * @param number $_pageCoverageMono
     * @return \Proposalgen_Model_Survey_Setting
     */
    public function setPageCoverageMono ($_pageCoverageMono)
    {
        $this->_pageCoverageMono = $_pageCoverageMono;
        return $this;
    }

    /**
     * Gets the color page coverage of the survey setting
     *
     * @return number
     */
    public function getPageCoverageColor ()
    {
        return $this->_pageCoverageColor;
    }

    /**
     * Sets the color page coverage of the survey setting
     *
     * @param number $_pageCoverageColor
     * @return \Proposalgen_Model_Survey_Setting
     */
    public function setPageCoverageColor ($_pageCoverageColor)
    {
        $this->_pageCoverageColor = $_pageCoverageColor;
        return $this;
    }
}