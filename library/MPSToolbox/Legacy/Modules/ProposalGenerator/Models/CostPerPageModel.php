<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class CostPerPageModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class CostPerPageModel extends My_Model_Abstract
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

    public $monochromeBreakDown = ['Hardware'=>0, 'Margin'=>0, 'Parts'=>0, 'Labor'=>0, 'Admin'=>0];
    public $colorBreakDown = ['Hardware'=>0, 'Margin'=>0, 'Parts'=>0, 'Labor'=>0, 'Admin'=>0];

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
        return [
            "monochromeCostPerPage" => $this->monochromeCostPerPage,
            "colorCostPerPage"      => $this->colorCostPerPage,
        ];
    }

    /**
     * Adds the two cost per pages together
     *
     * @param CostPerPageModel $costPerPage
     *            The cost per page object to add to this cost per page object
     */
    public function add (CostPerPageModel $costPerPage, $breakDownTo=null)
    {
        $this->monochromeCostPerPage += $costPerPage->monochromeCostPerPage;
        $this->colorCostPerPage += $costPerPage->colorCostPerPage;
        if ($breakDownTo) {
            $this->monochromeBreakDown[$breakDownTo] += $costPerPage->monochromeCostPerPage;
            $this->colorBreakDown[$breakDownTo] += $costPerPage->colorCostPerPage;
        }
    }
}