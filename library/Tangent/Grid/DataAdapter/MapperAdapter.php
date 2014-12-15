<?php

namespace Tangent\Grid\DataAdapter;

/**
 * Interface MapperAdapter
 *
 * An adapter for grids that utilizes mappers to provide data
 *
 * @package Tangent\Grid\DataAdapter
 */
class MapperAdapter implements DataAdapterInterface
{
    /**
     * @var \My_Model_Mapper_Abstract
     */
    protected $mapper;

    protected $startRecord = 0;
    protected $recordLimit = 10;

    /**
     * Constructor for MapperAdapter
     *
     * @param \My_Model_Mapper_Abstract $mapper
     */
    public function __construct (\My_Model_Mapper_Abstract $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Adds a filter
     *
     * @param $filter
     *
     * @return $this
     */
    public function addFilter ($filter)
    {
        // TODO: Implement addFilter() method.
        throw new \BadMethodCallException('Method not implemented yet.');
    }

    /**
     * Adds an order by
     *
     * @param $orderBy
     *
     * @return $this
     */
    public function addOrderBy ($orderBy)
    {
        // TODO: Implement addOrderBy() method.
        throw new \BadMethodCallException('Method not implemented yet.');
    }

    /**
     * Fetches the data and returns it in an array format consumable by grids
     *
     * @return array
     */
    public function fetchAll ()
    {
        // TODO: Implement fetchAll() method.
        throw new \BadMethodCallException('Method not implemented yet.');
    }

    /**
     * Gets the count of all the records available to the current **filtered** data set
     *
     * @return int
     */
    public function count ()
    {
        // TODO: Implement count() method.
        throw new \BadMethodCallException('Method not implemented yet.');
    }

    /**
     * Gets the count of all the records available to the current **un-filtered** data set
     *
     * @return int
     */
    public function countWithoutFilter ()
    {
        return $this->mapper->count();
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
        $this->recordLimit = $limit;

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
        $this->startRecord = $startRecord;

        return $this;
    }
}