<?php

namespace Tangent\Grid;

use Tangent\Grid\DataAdapter\DataAdapterInterface;
use Tangent\Grid\Request\RequestInterface;
use Tangent\Grid\Response\ResponseInterface;

/**
 * Class Grid
 *
 * @package Tangent\Grid
 */
class Grid
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var DataAdapterInterface
     */
    protected $dataAdapter;

    /**
     * Constructor for the grid
     *
     * @param RequestInterface     $request
     * @param ResponseInterface    $response
     * @param DataAdapterInterface $dataAdapter
     *
     * @throws \InvalidArgumentException
     */
    public function __construct (RequestInterface $request, ResponseInterface $response, DataAdapterInterface $dataAdapter)
    {
        // Request
        if (!$request instanceof RequestInterface)
        {
            throw new \InvalidArgumentException('$request must be an instance of RequestInterface');
        }
        $this->request = $request;

        // Response
        if (!$response instanceof ResponseInterface)
        {
            throw new \InvalidArgumentException('$response must be an instance of ResponseInterface');
        }
        $this->response = $response;

        // Data Adapter
        if (!$dataAdapter instanceof DataAdapterInterface)
        {
            throw new \InvalidArgumentException('$dataAdapter must be an instance of DataAdapterInterface');
        }
        $this->dataAdapter = $dataAdapter;

        $this->processRequest();
    }

    /**
     * Handles processing the request object
     */
    protected function processRequest ()
    {
        foreach ($this->request->getOrderColumns() as $orderColumn)
        {
            $this->dataAdapter->addOrderBy($orderColumn);
        }

        $this->dataAdapter->setStartRecord($this->request->getStartRecord());
        $this->dataAdapter->setLimit($this->request->getRecordFetchLimit());

        $this->response->setStartRecord($this->request->getStartRecord());
        $this->response->setRecordsPerPage($this->request->getRecordFetchLimit());
    }

    /**
     * Gets the response array
     *
     * @return array
     */
    public function getGridResponseAsArray ()
    {
        $this->response->setTotalRecords($this->dataAdapter->countWithoutFilter());
        $this->response->setTotalFilteredRecords($this->dataAdapter->count());
        $this->response->setData($this->dataAdapter->fetchAll()->toArray());

        return $this->response->getResponseForGrid();
    }
}