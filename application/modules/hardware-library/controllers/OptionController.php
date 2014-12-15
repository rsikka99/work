<?php

use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\ProposalgenAclModel;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\OptionEntity;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\AvailableOptionsForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\OptionService;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use Tangent\Controller\Action;
use Tangent\Service\JQGrid;

/**
 * Class HardwareLibrary_OptionController
 */
class HardwareLibrary_OptionController extends Action
{
    /**
     * @var bool
     */
    protected $isMasterHardwareAdmin;

    public function init ()
    {
        $this->isMasterHardwareAdmin = $this->view->IsAllowed(ProposalgenAclModel::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
    }

    /**
     * Displays all options
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('All Options');

        throw new Exception("Action not implemented!");
    }

    /**
     * Loads the available options form (really just the option form) for
     * an ajax call
     */
    public function loadFormAction ()
    {
        $optionId = $this->getParam('optionId', false);
        $option   = null;
        $form     = new AvailableOptionsForm();

        if ($optionId > 0)
        {
            $option = OptionMapper::getInstance()->find($optionId);
            if (!$option instanceof OptionModel)
            {
                $this->sendJson('Invalid Option');
            }

            $form->populate($option->toArray());
        }

        $this->view->form = $form;

        $this->getLayout()->disableLayout();
    }

    /**
     * Handles creating and saving option information
     *
     * @throws Zend_Controller_Response_Exception
     */
    public function saveAction ()
    {
        if ($this->getRequest()->isPost())
        {
            try
            {
                $dealerId      = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $optionService = new OptionService($dealerId);


                $creatingOption = false;
                $postData       = $this->getRequest()->getPost();

                $optionId = $this->getParam('optionId', false);
                if ((int)$optionId > 0)
                {
                    $optionEntity = $optionService->find($optionId);

                    if (!$optionEntity instanceof OptionEntity)
                    {
                        $this->sendJsonError('Invalid option ID');
                    }
                }
                else
                {
                    $creatingOption = true;
                }

                $form = new AvailableOptionsForm();

                if ($form->isValid($postData))
                {
                    $formData = $form->getValues();


                    if ($creatingOption)
                    {
                        $optionEntity = $optionService->create($formData);
                        $optionEntity->toArray();
                    }
                    else
                    {
                        $optionEntity = $optionService->update($optionId, $formData);

                        if (!$optionEntity instanceof OptionEntity)
                        {
                            throw new Exception("An unhandled error occurred while saving the option");
                        }
                    }

                    /**
                     * Send success message
                     */
                    $this->sendJson([
                        'message'  => 'Option saved successfully',
                        'optionId' => $optionEntity->id,
                    ]);
                }
                else
                {
                    $this->getResponse()->setHttpResponseCode(500);
                    $this->sendJson([
                        'message'       => 'Validation Error',
                        'errorMessages' => $form->getMessages(),
                    ]);
                }
            }
            catch (Exception $e)
            {
                \Tangent\Logger\Logger::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson([
                    'message' => 'Unhandled Exception',
                    'uid'     => \Tangent\Logger\Logger::getUniqueId(),
                ]);
            }
        }
        else
        {
            $this->sendJsonError('Invalid Method');
        }
    }

    public function deleteAction ()
    {
        if ($this->getRequest()->isPost())
        {
            try
            {
                $dealerId      = (int)Zend_Auth::getInstance()->getIdentity()->dealerId;
                $optionService = new OptionService($dealerId);

                $optionId = $this->getParam('optionId', false);
                if ((int)$optionId > 0)
                {
                    $optionEntity = $optionService->find($optionId);

                    if (!$optionEntity instanceof OptionEntity)
                    {
                        $this->sendJsonError('Invalid option ID');
                    }

                    if ((int)$optionEntity->dealerId !== $dealerId)
                    {
                        $this->sendJsonError('Invalid option ID.');
                    }

                    if ($optionEntity->delete())
                    {
                        $this->sendJson(['message' => 'Option deleted successfully.']);
                    }
                    else
                    {
                        $this->sendJsonError('An error occurred while deleting the option.');
                    }
                }
                else
                {
                    $this->sendJsonError('Invalid Option Id');
                }


            }
            catch (Exception $e)
            {
                \Tangent\Logger\Logger::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson([
                    'message' => 'Unhandled Exception',
                    'uid'     => \Tangent\Logger\Logger::getUniqueId(),
                ]);
            }
        }
        else
        {
            $this->sendJsonError('Invalid Method');
        }
    }

    /**
     * Gets all the options available to a quote device
     */
    public function optionListAction ()
    {
        $jsonArray        = array();
        $jqGridService    = new JQGrid();
        $optionMapper     = OptionMapper::getInstance();
        $masterDeviceId   = $this->_getParam('masterDeviceId', false);
        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'oemSku'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );

        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;

        $sortColumns = array(
            'oemSku',
            'dealerSku',
            'name',
            'option',
            'cost',
            'description',
        );
        $jqGridService->setValidSortColumns($sortColumns);
        $jqGridService->parseJQGridPagingRequest($jqGridParameters);

        if ($jqGridService->sortingIsValid())
        {
            $searchCriteria = null;
            $searchValue    = null;

            $options = $optionMapper->fetchAllOptionsWithDeviceOptions($masterDeviceId, $dealerId, null, $searchCriteria, $searchValue, 1000, 0);
            $jqGridService->setRecordCount(count($options));

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
            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }
            $options = $optionMapper->fetchAllOptionsWithDeviceOptions($masterDeviceId, $dealerId, $sortOrder, $searchCriteria, $searchValue, $jqGridService->getRecordsPerPage(), $startRecord);
            $jqGridService->setRows($options);

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                'error' => sprintf('Sort index "%s" is not a valid sorting index.', $jqGridService->getSortColumn())
            ));
        }
    }
}