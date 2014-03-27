<?php

/**
 * Class Admin_Event_LogController
 */
class Admin_Event_LogController extends Tangent_Controller_Action
{

    /**
     * Index action
     */
    public function indexAction ()
    {
        $postData = $this->getRequest()->getPost();

        if ($this->getRequest()->isPost())
        {
            if (isset($postData['clearAllLogs']))
            {
//                Application_Model_Mapper_Event_Log::getInstance()->deleteAllEventLogs();
                $this->_flashMessenger->addMessage(array('error' => 'Clearing disabled due to the lack of a confirmation dialog.'));
                $this->redirector('index');
            }
        }

        $users  = Application_Model_Mapper_User::getInstance()->fetchAll();
        $emails = array();
        foreach ($users as $user)
        {
            $emails[] = $user->email;
        }

        $eventLogTypes = Application_Model_Mapper_Event_Log_Type::getInstance()->fetchAll();
        $types         = array();
        foreach ($eventLogTypes as $eventLogType)
        {
            $types[$eventLogType->id] = $eventLogType->name;
        }

        $this->view->eventLogTypes = $types;
        $this->view->emails        = $emails;
    }

    /**
     * JqGrid Action
     * Gets the events for the event log JqGrid
     */
    public function getEventLogsAction ()
    {
        $jqGridService = new Tangent_Service_JQGrid();
        $email         = $this->_getParam('email', false);
        $type          = $this->_getParam('type', false);

        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'timestamp'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 20),
        );
        $sortColumns      = array(
            'email',
            'type',
            'description',
            'timestamp',
        );

        $jqGridService->setValidSortColumns($sortColumns);
        if ($jqGridService->sortingIsValid())
        {
            $eventLogMapper = Application_Model_Mapper_Event_Log::getInstance();

            $jqGridService->parseJQGridPagingRequest($jqGridParameters);
            try
            {
                $jqGridService->setRecordCount(count($eventLogMapper->fetchAllForJqGrid(null, 10000, 0, $email, $type)));

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

                $jqGridService->setRows($eventLogMapper->fetchAllForJqGrid($sortOrder, $jqGridService->getRecordsPerPage(), $startRecord, $email, $type));;
            }
            catch (Exception $e)
            {
                Tangent_Log::logException($e);
                $errorMessage = "Failed to get the events";
                $this->sendJsonError($errorMessage);
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

    /**
     * JSON ACTION: Handles searching users by email
     */
    public function searchForEmailAction ()
    {
        $searchTerm = $this->getParam('emailName', false);
        $results    = array();

        if ($searchTerm !== false)
        {
            foreach (Application_Model_Mapper_User::getInstance()->searchByEmail($searchTerm) as $user)
            {
                $results[] = array(
                    "id"   => $user->email,
                    "text" => $user->email
                );
            }
        }

        $this->sendJson($results);
    }
}

