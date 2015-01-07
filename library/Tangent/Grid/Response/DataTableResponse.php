<?php

namespace Tangent\Grid\Response;

/**
 * Class DataTableResponse
 *
 * @package Tangent\Grid\Response
 */
class DataTableResponse extends AbstractResponse
{
    /**
     * Returns an array that can be JSONified and sent back to the client
     *
     * @return array
     */
    public function getResponseForGrid ()
    {
        $response = array(
            'draw'            => $this->requestIdentifier,
            'currentPage'     => $this->getCurrentPage(),
            'data'            => $this->data,
            'recordsPerPage'  => $this->recordsPerPage,
            'recordsTotal'    => $this->totalRecords,
            'recordsFiltered' => $this->totalFilteredRecords,
        );

        if (isset($this->errorMessage) && strlen($this->errorMessage) > 0)
        {
            $response['error'] = $this->errorMessage;
        }

        return $response;
    }
}