<?php

namespace Tangent\Grid\Response;

use Tangent\Grid\Order\Column;
use Tangent\Grid\Order\ColumnFactory;
use Tangent\Grid\Request\RequestInterface;

/**
 * Class AbstractResponse
 *
 * @package Tangent\Grid\Response
 */
abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var int
     */
    protected $totalRecords = 0;

    /**
     * @var int
     */
    protected $totalFilteredRecords = 0;

    /**
     * @var int
     */
    protected $startRecord = 1;

    /**
     * @var int
     */
    protected $recordsPerPage = 10;

    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var string
     */
    protected $requestIdentifier = 0;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Returns an array that can be JSONified and sent back to the client
     *
     * @return array
     */
    public function getResponseForGrid ()
    {
        $response = array(
            'currentPage'          => $this->getCurrentPage(),
            'data'                 => $this->data,
            'recordsPerPage'       => $this->recordsPerPage,
            'totalRecords'         => $this->totalRecords,
            'totalPages'           => $this->getTotalPages(),
            'totalFilteredRecords' => $this->totalFilteredRecords,
            'totalFilteredPages'   => $this->getTotalFilteredPages(),
        );

        if (isset($this->errorMessage) && strlen($this->errorMessage) > 0)
        {
            $response['error'] = $this->errorMessage;
        }

        return $response;
    }


    /**
     * Simple getter for data
     *
     * @return array
     */
    public function getData ()
    {
        return $this->data;
    }

    /**
     * Sets the data
     *
     * @param array $data The actual data to be presented in the grid.
     *
     * @return $this
     */
    public function setData ($data)
    {
        if (is_array($data))
        {
            $this->data = $data;
        }
        else
        {
            throw new \InvalidArgumentException('$data must be an array!');
        }

        return $this;
    }

    /**
     * Simple getter for total records
     *
     * @return int
     */
    public function getTotalRecords ()
    {
        return $this->totalRecords;
    }

    /**
     * Sets the total number of records that could be displayed to the grid
     *
     * @param int $totalRecords
     *
     * @return $this
     */
    public function setTotalRecords ($totalRecords)
    {
        $this->totalRecords = (int)$totalRecords;

        return $this;
    }

    /**
     * Simple getter for total filtered records
     *
     * @return int
     */
    public function getTotalFilteredRecords ()
    {
        return $this->totalFilteredRecords;
    }

    /**
     * Sets the total number of records after the grids filters are applied.
     *
     * @param int $totalFilteredRecords
     *
     * @return $this
     */
    public function setTotalFilteredRecords ($totalFilteredRecords)
    {
        $this->totalFilteredRecords = (int)$totalFilteredRecords;

        return $this;
    }

    /**
     * Simple getter for records per page
     *
     * @return int
     */
    public function getRecordsPerPage ()
    {
        return $this->recordsPerPage;
    }

    /**
     * Sets the number of records that are displayed per page
     *
     * @param int $recordsPerPage
     *
     * @return $this
     */
    public function setRecordsPerPage ($recordsPerPage)
    {
        $this->recordsPerPage = (int)$recordsPerPage;

        return $this;
    }

    /**
     * Simple getter for start record
     *
     * @return int
     */
    public function getStartRecord ()
    {
        return $this->startRecord;
    }

    /**
     * Sets the current start record index
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

    /**
     * Gets the current page that we're on
     *
     * @return int
     */
    public function getCurrentPage ()
    {
        if ($this->totalFilteredRecords > 0)
        {
            return (int)ceil($this->startRecord / $this->recordsPerPage) + 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Sets the current page
     * (Uses records per page * current page to determine the start record)
     *
     * @param int $currentPage
     *
     * @return int
     */
    public function setCurrentPage ($currentPage)
    {
        $this->startRecord = (int)$currentPage * $this->recordsPerPage;

        return $this;
    }

    /**
     * Returns the total number of pages with the filter applied to the data
     *
     * @return int
     */
    public function getTotalFilteredPages ()
    {
        if ($this->totalFilteredRecords > $this->recordsPerPage)
        {
            return ceil($this->totalFilteredRecords / $this->recordsPerPage);
        }
        else if ($this->totalFilteredRecords > 0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Returns the total number of pages with no filters applied to the data
     *
     * @return int
     */
    public function getTotalPages ()
    {
        if ($this->totalRecords > $this->recordsPerPage)
        {
            return (int)ceil($this->totalRecords / $this->recordsPerPage);
        }
        else if ($this->totalRecords > 0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Getter for requestIdentifier
     *
     * @return string
     */
    public function getRequestIdentifier ()
    {
        return $this->requestIdentifier;
    }

    /**
     * Sets the request identifier
     *
     * @param mixed $identifier
     *
     * @return $this
     */
    public function setRequestIdentifier ($identifier)
    {
        $this->requestIdentifier = $identifier;

        return $this;
    }


}