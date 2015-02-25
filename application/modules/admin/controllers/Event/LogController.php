<?php
use MPSToolbox\Legacy\Mappers\EventLogTypeMapper;
use MPSToolbox\Legacy\Mappers\EventLogMapper;
use MPSToolbox\Legacy\Mappers\UserMapper;
use Tangent\Controller\Action;
use Tangent\Service\JQGrid;

/**
 * Class Admin_Event_LogController
 */
class Admin_Event_LogController extends Action
{

    /**
     * Index action
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Event Log'];
        $postData         = $this->getRequest()->getPost();

        if ($this->getRequest()->isPost())
        {
            if (isset($postData['clearAllLogs']))
            {
//                MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->deleteAllEventLogs();
                $this->_flashMessenger->addMessage(['error' => 'Clearing disabled due to the lack of a confirmation dialog.']);
                $this->redirectToRoute('admin.event-log');
            }
        }

        $users  = UserMapper::getInstance()->fetchAll();
        $emails = [];
        foreach ($users as $user)
        {
            $emails[] = $user->email;
        }

        $eventLogTypes = EventLogTypeMapper::getInstance()->fetchAll();
        $types         = [];
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
        $jqGridService = new JQGrid();
        $email         = $this->_getParam('email', false);
        $type          = $this->_getParam('type', false);

        $jqGridParameters = [
            'sidx' => $this->_getParam('sidx', 'timestamp'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 20),
        ];
        $sortColumns      = [
            'email',
            'type',
            'description',
            'timestamp',
        ];

        $jqGridService->setValidSortColumns($sortColumns);
        $jqGridService->parseJQGridPagingRequest($jqGridParameters);

        if ($jqGridService->sortingIsValid())
        {
            $eventLogMapper = EventLogMapper::getInstance();

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

                $sortOrder = [];
                if ($jqGridService->hasColumns())
                {
                    $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
                }

                $jqGridService->setRows($eventLogMapper->fetchAllForJqGrid($sortOrder, $jqGridService->getRecordsPerPage(), $startRecord, $email, $type));;
            }
            catch (Exception $e)
            {
                \Tangent\Logger\Logger::logException($e);
                $errorMessage = "Failed to get the events";
                $this->sendJsonError($errorMessage);
            }
            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(['error' => 'Sorting parameters are invalid']);
        }
    }

    /**
     * JSON ACTION: Handles searching users by email
     */
    public function searchForEmailAction ()
    {
        $searchTerm = $this->getParam('emailName', false);
        $results    = [];

        if ($searchTerm !== false)
        {
            foreach (UserMapper::getInstance()->searchByEmail($searchTerm) as $user)
            {
                $results[] = [
                    "id"   => $user->email,
                    "text" => $user->email
                ];
            }
        }

        $this->sendJson($results);
    }
}

