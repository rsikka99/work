<?php

class Tangent_Service_JQGrid
{
    const JQGRID_SORT_ASC  = 'asc';
    const JQGRID_SORT_DESC = 'desc';

    /**
     * The column to sort by
     *
     * @var array
     */
    protected $_validSortColumns = array();

    /**
     * The column to group by
     *
     * @var array
     */
    protected $_validGroupByColumns = array();

    /**
     * The column to sort by
     *
     * @var string
     */
    protected $_sortColumn;

    /**
     * The direction to sort the column in
     *
     * @var string
     */
    protected $_sortDirection;

    /**
     * The column to group by
     *
     * @var string
     */
    protected $_groupByColumn;

    /**
     * The direction to sort the group by column
     *
     * @var string
     */
    protected $_groupBySortOrder;

    /**
     * The rows of data to send back to the jqgrid client
     *
     * @var int
     */
    protected $_rows;

    /**
     * The total number of records available
     *
     * @var int
     */
    protected $_recordCount = 0;

    /**
     * The current page that jqgrid is requesting
     *
     * @var int
     */
    protected $_currentPage = 1;

    /**
     * How many records are being displayed per page
     *
     * @var int
     */
    protected $_recordsPerPage = 0;

    /**
     * Takes in the post data from a jqgrid ajax call during pagination and assigns the values in this class
     *
     * @param array $postData
     *            The array of data to parse
     */
    public function parseJQGridPagingRequest ($postData)
    {
        if (is_array($postData))
        {
            $postData = new ArrayObject($postData, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($postData->sidx) && !is_null($postData->sidx))
        {
            /**
             * Separate Data
             */
            $sortIndexSections = explode(', ', $postData->sidx);

            if (count($sortIndexSections) > 1)
            {
                $this->setSortColumn($sortIndexSections[1]);

                $groupByParameters = explode(' ', $sortIndexSections[0]);

                if (count($groupByParameters) === 2)
                {
                    $this->_groupByColumn    = $groupByParameters[0];
                    $this->_groupBySortOrder = $groupByParameters[1];
                }
            }
            else
            {
                $this->setSortColumn($sortIndexSections[0]);
            }
        }

        if (isset($postData->sord) && !is_null($postData->sord))
        {
            $this->setSortDirection($postData->sord);
        }

        if (isset($postData->page) && !is_null($postData->page))
        {
            $this->setCurrentPage($postData->page);
        }

        if (isset($postData->rows) && !is_null($postData->rows))
        {
            $this->setRecordsPerPage($postData->rows);
        }
    }

    /**
     * Creates a valid response for the jqgrid paging ajax call
     *
     * @return array
     */
    public function createPagerResponseArray ()
    {
        $jsonResponse = array(
            'page'    => $this->getCurrentPage(),
            'total'   => $this->calculateTotalPages(),
            'records' => $this->getRecordCount(),
            'rows'    => $this->getRows()
        );

        return $jsonResponse;
    }

    /**
     * Validates all the information for sorting
     *
     * @return boolean
     */
    public function sortingIsValid ()
    {
        $isValid = true;
        // Sort column must exist in our valid columns list
        if (!in_array($this->getSortColumn(), $this->getValidSortColumns()) && !empty($this->_sortColumn))
        {
            $isValid = false;
        }

        if ($this->_groupByColumn !== null)
        {
            if ((strcasecmp(self::JQGRID_SORT_ASC, $this->getGroupBySortOrder()) !== 0) && (strcasecmp(self::JQGRID_SORT_DESC, $this->getGroupBySortOrder()) !== 0))
            {
                $isValid = false;
            }

            if (!in_array($this->getGroupByColumn(), $this->getValidGroupByColumns()))
            {
                $isValid = false;
            }
        }

        // Sort direction can only be ASC or DESC
        if ((strcasecmp(self::JQGRID_SORT_ASC, $this->getSortDirection()) !== 0) && (strcasecmp(self::JQGRID_SORT_DESC, $this->getSortDirection()) !== 0) && isset($this->_sortDirection))
        {
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * @return array
     */
    public function getValidSortColumns ()
    {
        return $this->_validSortColumns;
    }

    /**
     * Setter for $_validSortColumns
     *
     * @param array $_validSortColumns
     *                 The new value
     *
     * @return \Tangent_Service_JQGrid
     */
    public function setValidSortColumns ($_validSortColumns)
    {
        $this->_validSortColumns = $_validSortColumns;

        return $this;
    }

    /**
     * Getter for $_sortColumn
     *
     * @return string
     */
    public function getSortColumn ()
    {
        return $this->_sortColumn;
    }

    /**
     * Setter for $_sortColumn
     *
     * @param string $_sortColumn
     *            The new value
     *
     * @return \Tangent_Service_JQGrid
     */
    public function setSortColumn ($_sortColumn)
    {
        $this->_sortColumn = $_sortColumn;

        return $this;
    }

    /**
     * Getter for $_sortDirection
     *
     * @return string
     */
    public function getSortDirection ()
    {
        return $this->_sortDirection;
    }

    /**
     * Setter for $_sortDirection
     *
     * @param string $_sortDirection
     *            The new value
     *
     * @return \Tangent_Service_JQGrid
     */
    public function setSortDirection ($_sortDirection)
    {
        $this->_sortDirection = $_sortDirection;

        return $this;
    }

    /**
     * Getter for $_rows
     *
     * @return number
     */
    public function getRows ()
    {
        return $this->_rows;
    }

    /**
     * Setter for $_rows
     *
     * @param number|array $_rows
     *            The new value
     *
     * @return \Tangent_Service_JQGrid
     */
    public function setRows ($_rows)
    {
        $this->_rows = $_rows;

        return $this;
    }

    /**
     * Getter for $_recordCount
     *
     * @return number
     */
    public function getRecordCount ()
    {
        return $this->_recordCount;
    }

    /**
     * Setter for $_recordCount
     *
     * @param number $_recordCount
     *            The new value
     *
     * @return \Tangent_Service_JQGrid
     */
    public function setRecordCount ($_recordCount)
    {
        $this->_recordCount = (int)$_recordCount;

        return $this;
    }

    /**
     * Getter for $_currentPage
     *
     * @return number
     */
    public function getCurrentPage ()
    {
        return $this->_currentPage;
    }

    /**
     * Setter for $_currentPage
     *
     * @param number $_currentPage
     *            The new value
     *
     * @return \Tangent_Service_JQGrid
     */
    public function setCurrentPage ($_currentPage)
    {
        $this->_currentPage = (int)$_currentPage;

        return $this;
    }

    /**
     * Getter for $_recordsPerPage
     *
     * @return number
     */
    public function getRecordsPerPage ()
    {
        return $this->_recordsPerPage;
    }

    /**
     * Setter for $_recordsPerPage
     *
     * @param number $_recordsPerPage
     *            The new value
     *
     * @return \Tangent_Service_JQGrid
     */
    public function setRecordsPerPage ($_recordsPerPage)
    {
        $this->_recordsPerPage = (int)$_recordsPerPage;

        return $this;
    }

    /**
     * Calculates how many pages of data we have
     *
     * @return number
     */
    public function calculateTotalPages ()
    {
        $totalPages = 0;
        if ($this->getRecordCount() > 0 && $this->getRecordsPerPage() > 0)
        {
            $totalPages = ceil($this->getRecordCount() / $this->getRecordsPerPage());
        }

        return $totalPages;
    }

    /**
     * Setter for _groupByColumn
     *
     * @param string $groupByColumn
     */
    public function setGroupByColumn ($groupByColumn)
    {
        $this->_groupByColumn = $groupByColumn;
    }

    /**
     * Getter for _groupByColumn
     *
     * @return string
     */
    public function getGroupByColumn ()
    {
        if (!isset($this->_groupByColumn))
        {
            $this->_groupByColumn = null;
        }

        return $this->_groupByColumn;
    }

    /**
     * Setter for _groupBySortOrder
     *
     * @param string $groupBySortOrder
     */
    public function setGroupBySortOrder ($groupBySortOrder)
    {
        $this->_groupBySortOrder = $groupBySortOrder;
    }

    /**
     * Getter for _groupBySortOrder
     *
     * @return string
     */
    public function getGroupBySortOrder ()
    {
        if (!isset($this->_groupBySortOrder))
        {
            $this->_groupBySortOrder = null;
        }

        return $this->_groupBySortOrder;
    }

    /**
     * Setter for _validGroupByColumns
     *
     * @param array $validGroupByColumns
     */
    public function setValidGroupByColumns ($validGroupByColumns)
    {
        $this->_validGroupByColumns = $validGroupByColumns;
    }

    /**
     * Getter for _validGroupByColumns
     *
     * @return array
     */
    public function getValidGroupByColumns ()
    {
        if (!isset($this->_validGroupByColumns))
        {
            $this->_validGroupByColumns = null;
        }

        return $this->_validGroupByColumns;
    }

    /**
     * Returns whether or not grouping should take effect
     *
     * @return bool
     */
    public function hasGrouping ()
    {
        return ($this->_groupByColumn !== null && $this->_groupBySortOrder !== null);
    }


    /**
     * Return whether or not we have columns to sort by
     *
     * @return bool
     */
    public function hasColumns ()
    {
        return (!empty($this->_sortColumn) && !empty($this->_sortDirection));
    }
}