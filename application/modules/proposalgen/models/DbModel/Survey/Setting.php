<?php

/**
 * Class Application_Model_Survey_Setting
 */
class Proposalgen_Model_DbModel_Survey_Setting extends Tangent_Model_Abstract
{
    protected $Id;
    protected $PageCoverageColor;
    protected $PageCoverageMono;

    /**
     *
     * @return the $Id
     */
    public function getId ()
    {
        if (! isset($this->Id))
        {
            
            $this->Id = null;
        }
        return $this->Id;
    }

    /**
     *
     * @param $Id field_type            
     */
    public function setId ($Id)
    {
        $this->Id = $Id;
        return $this;
    }

    /**
     *
     * @return the $PageCoverageColor
     */
    public function getPageCoverageColor ()
    {
        if (! isset($this->PageCoverageColor))
        {
            
            $this->PageCoverageColor = null;
        }
        return $this->PageCoverageColor;
    }

    /**
     *
     * @param $PageCoverageColor field_type            
     */
    public function setPageCoverageColor ($PageCoverageColor)
    {
        $this->PageCoverageColor = $PageCoverageColor;
        return $this;
    }

    /**
     *
     * @return the $PageCoverageMono
     */
    public function getPageCoverageMono ()
    {
        if (! isset($this->PageCoverageMono))
        {
            
            $this->PageCoverageMono = null;
        }
        return $this->PageCoverageMono;
    }

    /**
     *
     * @param $PageCoverageMono field_type            
     */
    public function setPageCoverageMono ($PageCoverageMono)
    {
        $this->PageCoverageMono = $PageCoverageMono;
        return $this;
    }
}
