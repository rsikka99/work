<?php

namespace Tangent\Grid\Response;

/**
 * Class JqGridResponse
 *
 * @package Tangent\Grid\Response
 */
class JqGridResponse extends AbstractResponse
{
    /**
     * Returns an array that can be JSONified and sent back to the client
     *
     * @return array
     */
    public function getResponseForGrid ()
    {
        return array(
            'page'    => $this->getCurrentPage(),
            'total'   => $this->getTotalFilteredPages(),
            'records' => $this->recordsPerPage,
            'rows'    => $this->data
        );
    }
}