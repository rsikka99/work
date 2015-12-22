<?php

namespace MPSToolbox\Grid\DataAdapter;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use Tangent\Grid\DataAdapter\DataAdapterInterface;
use Tangent\Grid\Filter\AbstractFilter;
use Tangent\Grid\Order\Column;

/**
 * Class MasterDeviceDataAdapter
 *
 * @package MPSToolbox\Grid\DataAdapter
 */
class MasterDeviceDataAdapter implements DataAdapterInterface
{
    /**
     * @var MasterDeviceMapper
     */
    protected $mapper;

    /**
     * @var AbstractFilter[]
     */
    protected $filters = [];

    /**
     * @var Column[]
     */
    protected $orderBy = [];

    /**
     * @var Column[]
     */
    protected $defaultOrderBy = [];

    protected $startRecord = 0;
    protected $limit       = 10;

    protected $onlyQuoteDevices = false;

    /**
     * @param MasterDeviceMapper $mapper
     * @param bool               $onlyQuoteDevices
     * @param array              $defaultOrder
     */
    public function __construct (MasterDeviceMapper $mapper, $onlyQuoteDevices, $defaultOrder = [])
    {
        $this->mapper           = $mapper;
        $this->onlyQuoteDevices = $onlyQuoteDevices;

        if (count($defaultOrder) > 0)
        {
            foreach ($defaultOrder as $columnName => $column)
            {
                $this->defaultOrderBy[$columnName] = $column;
            }
        }
        else
        {
            $this->defaultOrderBy['deviceName'] = new Column('modelName', Column::ORDER_ASCENDING);
        }
    }

    /**
     * Adds a filter
     *
     * @param \Tangent\Grid\Filter\AbstractFilter $filter
     *
     * @return $this
     */
    public function addFilter (AbstractFilter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Adds an order by
     *
     * @param \Tangent\Grid\Order\Column $orderBy
     *
     * @return $this
     */
    public function addOrderBy (Column $orderBy)
    {
        $this->orderBy[$orderBy->getColumnName()] = $orderBy;

        return $this;
    }

    /**
     * Gets the order by. Returns a default setting if no orders were added
     *
     * @return \Tangent\Grid\Order\Column[]
     */
    public function getOrderBy ()
    {
        return (count($this->orderBy) > 0) ? $this->orderBy : $this->defaultOrderBy;
    }

    /**
     * Resets the order by
     *
     * @return $this
     */
    public function resetOrderBy ()
    {
        $this->orderBy = [];

        return $this;
    }

    /**
     * Fetches the data and returns it in an array format consumable by grids
     *
     * @return array
     */
    public function fetchAll ()
    {
        $result = $this->mapper->getCanSellMasterDevices($this->getOrderBy(), $this->filters, $this->limit, $this->startRecord, $this->onlyQuoteDevices);
        $dealerId = \Zend_Auth::getInstance()->getIdentity()->dealerId;
        foreach ($result as $i=>$line) {
            if ($line['online']) {
                $result[$i]['online'] = '<input type="checkbox" checked="checked" onclick="online_click(this, '.$line['id'].', '.$dealerId.')">';
            } else {
                $result[$i]['online'] = '<input type="checkbox" onclick="online_click(this, '.$line['id'].', '.$dealerId.')">';
            }
        }
        return $result;
    }

    /**
     * Gets the count of all the records available to the current **filtered** data set
     *
     * @return int
     */
    public function count ()
    {
        return $this->mapper->getCanSellMasterDevices($this->getOrderBy(), $this->filters, null, null, $this->onlyQuoteDevices, true);
    }

    /**
     * Gets the count of all the records available to the current **un-filtered** data set
     *
     * @return int
     */
    public function countWithoutFilter ()
    {
        return $this->mapper->getCanSellMasterDevices($this->getOrderBy(), [], null, null, $this->onlyQuoteDevices, true);
    }

    /**
     * Sets how many records will be returned
     *
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit ($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * Sets the start record for fetching
     *
     * @param int $startRecord
     *
     * @return $this
     */
    public function setStartRecord ($startRecord)
    {
        $this->startRecord = (int)$startRecord;

        return $this;
    }
}