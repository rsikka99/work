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
            $this->setSortColumn($postData->sidx);
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
        // Sort column must exist in our valid columns list
        if (!in_array($this->getSortColumn(), $this->getValidSortColumns()))
        {
            return false;
        }

        // Sort direction can only be ASC or DESC
        if ((strcasecmp(self::JQGRID_SORT_ASC, $this->getSortDirection()) !== 0) && (strcasecmp(self::JQGRID_SORT_DESC, $this->getSortDirection()) !== 0))
        {
            return false;
        }

        return true;
    }

    /**
     * Validates all the information
     *
     * @return boolean
     */
    public function isValid ()
    {
        if (!$this->sortingIsValid())
        {
            return false;
        }

        return true;
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
}

