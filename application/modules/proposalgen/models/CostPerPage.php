<?php
/**
 * Class Proposalgen_Model_CostPerPage
 */
class Proposalgen_Model_CostPerPage extends My_Model_Abstract
{
    /**
     * The monochrome cost per page
     *
     * @var number
     */
    public $monochromeCostPerPage = 0;

    /**
     * The color cost per page
     *
     * @var number
     */
    public $colorCostPerPage = 0;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->monochromeCostPerPage) && !is_null($params->monochromeCostPerPage))
        {
            $this->monochromeCostPerPage = $params->monochromeCostPerPage;
        }

        if (isset($params->colorCostPerPage) && !is_null($params->colorCostPerPage))
        {
            $this->colorCostPerPage = $params->colorCostPerPage;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "monochromeCostPerPage" => $this->monochromeCostPerPage,
            "colorCostPerPage"      => $this->colorCostPerPage,
        );
    }

    /**
     * Adds the two cost per pages together
     *
     * @param Proposalgen_Model_CostPerPage $costPerPage
     *            The cost per page object to add to this cost per page object
     */
    public function add (Proposalgen_Model_CostPerPage $costPerPage)
    {
        $this->monochromeCostPerPage += $costPerPage->monochromeCostPerPage;
        $this->colorCostPerPage += $costPerPage->colorCostPerPage;
    }
}