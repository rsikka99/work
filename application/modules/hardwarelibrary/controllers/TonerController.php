<?php

/**
 * Class Hardwarelibrary_TonerController
 */
class Hardwarelibrary_TonerController extends Tangent_Controller_Action
{

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        $this->view->manufacturers       = Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAllAvailableManufacturers();
        $this->view->tonerColors         = Proposalgen_Model_Mapper_TonerColor::getInstance()->fetchAll();
        $this->view->availableTonersForm = new Proposalgen_Form_MasterDeviceManagement_AvailableToners(null, 0);
        $this->view->delete              = new Proposalgen_Form_MasterDeviceManagement_Delete();
    }

    /**
     * Fetches all the toners
     */
    public function allTonersListAction ()
    {
        $jqGridService  = new Tangent_Service_JQGrid();
        $tonerId        = $this->_getParam('tonerId', false);
        $manufacturerId = $this->_getParam('manufacturerId', false);
        $filter         = $this->_getParam('filter', false);
        $criteria       = $this->_getParam('criteria', false);

        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'id'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );
        $sortColumns      = array(
            'oemSku',
            'dealerSku',
            'name',
            'option',
            'cost',
            'description',
        );
        $jqGridService->setValidSortColumns($sortColumns);
        if ($jqGridService->sortingIsValid())
        {
            $tonerMapper = Proposalgen_Model_Mapper_Toner::getInstance();

            $jqGridService->parseJQGridPagingRequest($jqGridParameters);
            if ($filter == 'tonerColorId')
            {
                $jqGridService->setRecordCount(count($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorId($criteria, null, 10000, 0, $manufacturerId, $filter, $criteria)));
            }
            else
            {
                $jqGridService->setRecordCount(count($tonerMapper->fetchAllTonersWithMachineCompatibility(null, 10000, 0, $filter, $criteria, $manufacturerId)));
            }

            // Validate current page number since we don't want to be out of bounds
            if ($jqGridService->getCurrentPage() < 1)
            {
                $jqGridService->setCurrentPage(1);
            }
            else if ($jqGridService->getCurrentPage() > $jqGridService->calculateTotalPages())
            {
                $jqGridService->setCurrentPage($jqGridService->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGridService->getRecordsPerPage() * ($jqGridService->getCurrentPage() - 1);

            if ($startRecord < 0)
            {
                $startRecord = 0;
            }

            $sortOrder = array();
            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }

            if ($filter == 'tonerColorId')
            {
                $jqGridService->setRows($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorId($criteria, $sortOrder, $jqGridService->getRecordsPerPage(), $startRecord, $manufacturerId, $filter, $criteria));;
            }
            else
            {
                $jqGridService->setRows($tonerMapper->fetchAllTonersWithMachineCompatibility($sortOrder, $jqGridService->getRecordsPerPage(), $startRecord, $filter, $criteria, $manufacturerId));;
            }

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                                 'error' => 'Sorting parameters are invalid'
                            ));
        }
    }
}