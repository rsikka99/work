<?php

namespace Tangent\Grid\Response;
use Tangent\Grid\Request\DataTableRequest;

/**
 * Class DataTableResponse
 *
 * @package Tangent\Grid\Response
 */
class DataTableResponse extends AbstractResponse
{
    /**
     * Override constructor to ensure we always get a DataTable request
     *
     * @param \Tangent\Grid\Request\DataTableRequest $request
     */
    public function __construct (DataTableRequest $request)
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
        $response = [
            'draw'            => $this->request->getRequestIdentifier(),
            'currentPage'     => $this->getCurrentPage(),
            'data'            => $this->data,
            'recordsPerPage'  => $this->recordsPerPage,
            'recordsTotal'    => $this->totalRecords,
            'recordsFiltered' => $this->totalFilteredRecords,
        ];

        if (isset($this->errorMessage) && strlen($this->errorMessage) > 0)
        {
            $response['error'] = $this->errorMessage;
        }

        return $response;
    }
}