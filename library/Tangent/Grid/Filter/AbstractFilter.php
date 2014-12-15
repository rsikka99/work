<?php

namespace Tangent\Grid\Filter;

abstract class AbstractFilter
{
    /**
     * @var string
     */
    protected $filterIndex;

    /**
     * @var mixed
     */
    protected $filterValue;

    /**
     * Constructor for the search filter
     *
     * @param string $filterIndex
     * @param mixed  $filterValue
     */
    public function __construct ($filterIndex, $filterValue)
    {
        $this->filterIndex = $filterIndex;
        $this->filterValue = $filterValue;
    }

    /**
     * Gets the column name filter by
     *
     * @return string
     */
    public function getFilterIndex ()
    {
        return $this->filterIndex;
    }

    /**
     * Sets the column name to filter by
     *
     * @param string $filterIndex
     *
     * @return $this
     */
    public function setFilterIndex ($filterIndex)
    {
        $this->filterIndex = $filterIndex;

        return $this;
    }

    /**
     * Gets the filter value
     *
     * @return string
     */
    public function getFilterValue ()
    {
        return $this->filterValue;
    }

    /**
     * Sets filter value
     *
     * @param string $filterValue
     *
     * @return $this
     */
    public function setFilterValue ($filterValue)
    {
        $this->filterValue = $filterValue;

        return $this;
    }
}