<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\ProposalgenAclModel;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\AvailableTonersForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DeleteForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\TonerService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceTonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerColorMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use Tangent\Controller\Action;
use Tangent\Service\JQGrid;

/**
 * Class HardwareLibrary_TonerController
 */
class HardwareLibrary_TonerController extends Action
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
     * Displays all devices
     */
    public function indexAction ()
    {
        if (\MPSToolbox\Legacy\Services\NavigationService::$userId == 1) {
            header('Location: /hardware-library/toner/infinite');
            exit();
        }
        $this->_pageTitle = ['All Toners'];
    }

    private function outputInfiniteRow($line) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $cloud = $db->query("select * from cloud_file where type='image' and baseProductId={$line['id']} order by orderby limit 1")->fetch();

        $devices=[];
        $json = json_decode($line['json_device_list'], true);
        foreach ($json as $d) {
            $devices[] = '<a href="javascript:;" onclick="editDevice('.$d['id'].', '.$line['id'].')">'.$d['name'].'</a>';
        }
?>
        <div class="toner-item row" id="toner-<?= $line['id'] ?>">
            <div class="toner-image col-sm-3">
                <?php if ($cloud) { ?>
                <a href="javascript:" onclick="editRow(<?= $line['id'] ?>);" class="thumbnail">
                    <img src="<?= $cloud['url'] ?>" style="max-width:100%">
                </a>
                <?php } ?>
            </div>
            <div class="toner-info col-sm-4">
                <table class="table">
                    <tr><th>Sku: </th><td><a href="javascript:" onclick="editRow(<?= $line['id'] ?>);"><?= $line['systemSku'] ?></a></td></tr>
                    <tr><th>Manufacturer: </th><td><?= $line['manufacturer'] ?></td></tr>
                    <tr><th>Name: </th><td><?= $line['toner_name'] ?></td></tr>
                    <tr><th>Color: </th><td><?= $line['tonerColor'] ?></td></tr>
                    <tr><th>Yield: </th><td><?= number_format($line['yield'],0) ?></td></tr>
                    <tr><th>Cost: </th><td>$ <?= $line['base_systemCost'] ?></td></tr>
                </table>
            </div>
            <div class="toner-info col-sm-5">
                <table class="table">
<?php if (!empty($line['device_list'])) { ?>
                    <tr><th>Devices: </th><td><?= $line['device_list'] ?></td></tr>
<?php } else { ?>
                    <tr><th>Admin: </th><td><a class="btn btn-warning" href="javascript:" onclick="if (window.confirm('Delete this toner?')) deleteToner(<?= $line['id'] ?>)">Delete</a></td></tr>
<?php } ?>
                </table>
            </div>
        </div>
<?php
    }

    public function infiniteAction ()
    {
        if (\MPSToolbox\Legacy\Services\NavigationService::$userId != 1) {
            header('Location: /hardware-library/all-toners');
            exit();
        }

        if ($this->getRequest()->getMethod()=='POST') {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();

            $filterManufacturerId = $this->getParam('assign-toners-manufacturer-filter');
            $filterTonerSku = $this->getParam('assign-toners-filter-sku');
            $filterTonerColorId = $this->getParam('assign-toners-filter-color');

            $sortOrder = ['sku'];

            $offset = intval($this->getParam('offset'));
            $pageSize = 30;

            $tonerMapper = TonerMapper::getInstance();
            $count = $tonerMapper->countTonersForDealer($filterManufacturerId,$filterTonerSku,$filterTonerColorId);
            $arr = $tonerMapper->fetchTonersForDealer(
                $sortOrder,
                $pageSize,
                $offset,
                $filterManufacturerId,
                $filterTonerSku,
                $filterTonerColorId
            );

            foreach($arr as $line) {
                $this->outputInfiniteRow($line);
            }


            if ($count>$offset+$pageSize) {
                echo '<div id="load-more" data-offset="' . ($offset + $pageSize) . '"></div>';
            }

            return;
        }

        $reload = $this->getParam('reload');
        if ($reload) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $tonerMapper = TonerMapper::getInstance();
            $arr = $tonerMapper->fetchTonersForDealer(['sku'],1,0,false,false,false,$reload);
            foreach($arr as $line) {
                $this->outputInfiniteRow($line);
            }
            return;
        }

        $delete = $this->getParam('delete');
        if ($delete) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $tonerMapper = TonerMapper::getInstance();
            $tonerMapper->delete($delete);
            return;
        }

        $this->_pageTitle = ['All Toners'];
    }

    /**
     * Loads the available toners form (really just the toner form) for
     * an ajax call
     */
    public function loadFormAction ()
    {
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $dealer = \MPSToolbox\Legacy\Mappers\DealerMapper::getInstance()->find($dealerId);
        $tonerId = $this->getParam('tonerId', false);
        $toner   = null;
        if ($tonerId !== false)
        {
            $toner = TonerMapper::getInstance()->find($tonerId);
        }

        $isAdmin = \MPSToolbox\Legacy\Services\NavigationService::$userId == 1;  //$this->view->IsAllowed(ProposalgenAclModel::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
        $isAllowed                 = ((!$toner instanceof TonerModel || !$toner->isSystemDevice || $isAdmin) ? true : false);
        $this->view->isSystemDevice = $toner && $toner->isSystemDevice;
        $this->view->isAdmin = $isAdmin;
        $this->view->isAllowed = $isAllowed;
        $form = new AvailableTonersForm($dealerId, $toner, null, $isAllowed);;
        $form->distributors=[];
        #--
        if ($toner) {
            $attr = $toner->getDealerTonerAttribute($dealerId);
            if ($attr->cost) {
                $form->distributors[] = [
                    'name' => $attr->distributor ? $attr->distributor : $dealer->dealerName,
                    'sku' => $attr->dealerSku,
                    'price' => $attr->cost,
                    'stock' => '',
                ];
            }

            $i=new \MPSToolbox\Services\ImageService();
            $form->images = $i->getImageUrls($toner->id);
        }
        #--
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select s.name as supplier_name, supplierSku, price, isStock from supplier_product p join suppliers s on p.supplierId=s.id join supplier_price c using (supplierId, supplierSku) where dealerId='.$dealerId.' and baseProductId=:tonerId');
        $st->execute(['tonerId'=>$tonerId]);
        foreach ($st->fetchAll() as $line) {
            $form->distributors[] = [
                'name'=>$line['supplier_name'],
                'sku'=>$line['supplierSku'],
                'price'=>$line['price'],
                'stock'=>$line['isStock'],
            ];
        }
        #--
        $this->view->tonerForm = $form;
        $this->view->toner = $toner;

        $this->_helper->layout()->disableLayout();
    }

    /**
     * Handles creating and saving toner information
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
                $userId        = Zend_Auth::getInstance()->getIdentity()->id;
                $creatingToner = false;
                $postData      = $this->getRequest()->getPost();

                $tonerId = $this->getParam('tonerId', false);
                if ((int)$tonerId > 0)
                {
                    $toner = TonerMapper::getInstance()->find($tonerId);

                    if (!$toner instanceof TonerModel)
                    {
                        $this->sendJsonError('Invalid toner ID');
                    }
                }
                else
                {
                    $toner         = new TonerModel();
                    $creatingToner = true;
                }

                $isAdmin = \MPSToolbox\Legacy\Services\NavigationService::$userId == 1;  //$this->view->IsAllowed(ProposalgenAclModel::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
                $isAllowed                 = ((!$toner instanceof TonerModel || !$toner->isSystemDevice || $isAdmin) ? true : false);
                $form = new AvailableTonersForm($dealerId, $toner, null, $isAllowed);

                if ($form->isValid($postData))
                {
                    $formData     = $form->getValues();

                    $tonerService = new TonerService($userId, $dealerId, $this->isMasterHardwareAdmin);

                    if ($creatingToner)
                    {
                        $toner = $tonerService->createToner($formData, $userId);
                    }
                    else
                    {
                        /**
                        if (isset($formData['tonerColorId']) && ((int)$toner->tonerColorId !== (int)$formData['tonerColorId']))
                        {
                            $deviceToners = DeviceTonerMapper::getInstance()->fetchDeviceTonersByTonerId($toner->id);
                            if (count($deviceToners) > 0)
                            {
                                $this->getResponse()->setHttpResponseCode(500);
                                $this->sendJson([
                                    'message'       => 'Validation Error',
                                    'errorMessages' => [
                                        'tonerColorId' => [
                                            'assignedToner' => 'You cannot change the color of a toner while it\'s assigned to devices',
                                        ]
                                    ],
                                ]);
                            }
                        }
                        **/

                        if (!empty($formData['cost']) && !\MPSToolbox\Services\CurrencyService::isUSD()) {
                            $toner = TonerMapper::getInstance()->find($tonerId);
                            $toner->setLocalCost($formData['cost']);
                            unset($formData['cost']);
                        }

                        $toner = $tonerService->updateToner($tonerId, $formData);

                        if (!$toner instanceof TonerModel)
                        {
                            throw new Exception("An unhandled error occurred while saving the toner");
                        }
                    }

                    /**
                     * Dealer Attributes
                     */
                    $tonerService->saveDealerAttributes($toner, $formData);

                    /**
                     * Send success message
                     */
                    $this->sendJson([
                        'message' => 'Toner saved successfully',
                        'tonerId' => $toner->id,
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

    public function imageAction() {
        $tonerId = $this->_getParam('id', false);
        $toner = TonerMapper::getInstance()->find($tonerId);
        if (!$toner) {
            $this->sendJsonError('not found');
            return;
        }

        $isAllowed = ((!$toner instanceof TonerModel || !$toner->isSystemDevice || $this->isMasterHardwareAdmin) ? true : false);
        if (!$isAllowed) {
            $this->sendJsonError('not allowed');
            return;
        }
        $dealerId      = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $userId        = Zend_Auth::getInstance()->getIdentity()->id;
        $tonerService = new TonerService($userId, $dealerId, $this->isMasterHardwareAdmin);
        foreach ($_FILES as $upload) {
            $tonerService->uploadImage($toner, $upload);
            TonerMapper::getInstance()->save($toner);
        }

        $result = array(
            'filename'=>$toner->imageFile
        );
        $this->sendJson($result);
    }

    /**
     * Handles creating and saving dealer toner information
     *
     * @throws Zend_Controller_Response_Exception
     */
    public function saveDealerAttributesAction ()
    {
        if ($this->getRequest()->isPost())
        {
            try
            {
                $userId   = Zend_Auth::getInstance()->getIdentity()->id;
                $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $creating = false;
                $postData = $this->getRequest()->getPost();

                $tonerId = $this->getParam('tonerId', false);
                if ((int)$tonerId > 0)
                {
                    $dealerTonerAttribute = DealerTonerAttributeMapper::getInstance()->find([$tonerId, $dealerId]);

                    if (!$dealerTonerAttribute instanceof DealerTonerAttributeModel)
                    {
                        $dealerTonerAttribute = new DealerTonerAttributeModel();
                        $creating             = true;
                    }
                }
                else
                {
                    $this->sendJsonError('You must provide a toner id');
                }

                $form = new AvailableTonersForm($dealerId, $dealerTonerAttribute);

                if ($form->isValid($postData))
                {
                    $formData = $form->getValues();

                    $tonerService = new TonerService($userId, $dealerId, $this->isMasterHardwareAdmin);

                    if ($creating)
                    {
                        $dealerTonerAttribute = $tonerService->createToner($formData);
                    }
                    else
                    {
                        $dealerTonerAttribute = $tonerService->updateToner($tonerId, $formData);

                        if (!$dealerTonerAttribute instanceof TonerModel)
                        {
                            throw new Exception("An unhandled error occurred while saving the toner");
                        }
                    }

                    /**
                     * Send success message
                     */
                    $this->sendJson([
                        'message' => 'Toner saved successfully',
                        'tonerId' => $dealerTonerAttribute->id,
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

    /**
     * Fetches all the toners
     */
    public function allTonersListAction ()
    {
        $filterManufacturerId = $this->_getParam('filterManufacturerId', false);
        $filterTonerSku       = $this->_getParam('filterTonerSku', false);
        $filterTonerPriced       = $this->_getParam('filterTonerPriced', false);
        $filterTonerColorId   = $this->_getParam('filterTonerColorId', false);

        $jqGridParameters = [
            'sidx' => $this->_getParam('sidx', 'manufacturer'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        ];

        $jqGridService = new JQGrid();

        $jqGridService->setValidSortColumns([
            'id',
            'tonerColorId',
            'sku',
            'name',
            'dealerSku',
            'manufacturer',
            'yield',
            'dealerCost',
            'device_list',
        ]);

        $jqGridService->parseJQGridPagingRequest($jqGridParameters);
        $tonerMapper = TonerMapper::getInstance();

        if ($jqGridService->sortingIsValid())
        {
            $count = $tonerMapper->countTonersForDealer($filterManufacturerId,$filterTonerSku,$filterTonerColorId,$filterTonerPriced);
            $jqGridService->setRecordCount($count);

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

            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                if (strcasecmp($jqGridService->getSortColumn(), 'dealerCost') === 0)
                {
                    $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
                    $sortOrder[] = 'toners.cost' . ' ' . $jqGridService->getSortDirection();
                }
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }

            $jqGridService->setRows($tonerMapper->fetchTonersForDealer(
                $sortOrder,
                $jqGridService->getRecordsPerPage(),
                $startRecord,
                $filterManufacturerId,
                $filterTonerSku,
                $filterTonerColorId,
                false,
                $filterTonerPriced
            ));

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson([
                'error' => sprintf('Sort index "%s" is not a valid sorting index.', $jqGridService->getSortColumn())
            ]);
        }
    }

    /**
     * Returns a json list of toner colors for a toner configuration
     */
    public function colorsForConfigurationAction ()
    {
        $tonerColorList       = [];
        $tonerConfigurationId = $this->getParam('tonerConfigId', false);
        $tonerColorId         = $this->getParam('tonerColorId', false);

        if ($tonerColorId)
        {
            $tonerColor = TonerColorMapper::getInstance()->find($tonerColorId);
            if ($tonerColor instanceof TonerColorModel)
            {
                $this->sendJson([
                    "id"   => $tonerColor->id,
                    "text" => $tonerColor->name,
                ]);
            }
        }

        if ($tonerConfigurationId > 0)
        {
            if (!isset(TonerConfigModel::$TonerConfigNames[$tonerConfigurationId]))
            {
                $this->sendJsonError('Invalid Toner Configuration');
            }

            foreach (TonerConfigModel::getRequiredTonersForTonerConfig($tonerConfigurationId) as $name => $id)
            {
                $tonerColorList[] = [
                    "id"   => $id,
                    "text" => $name,
                ];
            }
        }
        else
        {
            foreach (TonerColorModel::$ColorNames as $tonerColorId => $tonerColorName)
            {
                $tonerColorList[] = [
                    "id"   => $tonerColorId,
                    "text" => $tonerColorName,
                ];
            }
        }


        $this->sendJson($tonerColorList);
    }

    public function addImageAction() {
        $baseProductId = $this->getParam('baseProductId');
        $url = $this->getParam('url');
        $result = [];
        if ($url) {
            $i = new \MPSToolbox\Services\ImageService();
            $cloud_url = $i->addImage($baseProductId, $url, \MPSToolbox\Services\ImageService::LOCAL_TONER_DIR, \MPSToolbox\Services\ImageService::TAG_TONER);
            if ($cloud_url) {
                $urls = $i->getImageUrls($baseProductId);
                $tr='';
                foreach ($urls as $id=>$url) {
                    $tr.='<tr>
                        <td><img src="'.$url.'" style="width:150px;max-height:150px">
                        <a href="javascript:;" onclick="deleteImage('.$id.')" style="color:red">delete</a></td>
                    </tr>';
                }
                if (!$tr) $tr='<tr><td>no images</td></tr>';
                $result['tr'] = $tr;
            } else {
                $result['error'] = 'Download from URL failed: '.$i->lastError;
            }
        } else {
            $result['error'] = 'No URL provided';
        }
        $this->sendJson($result);
    }

    public function deleteImageAction() {
        $baseProductId = $this->getParam('baseProductId');
        $id = $this->getParam('id');
        $i = new \MPSToolbox\Services\ImageService();
        $i->deleteImageById($id);
        $urls = $i->getImageUrls($baseProductId);
        $tr='';
        foreach ($urls as $id=>$url) {
            $tr.='<tr>
                        <td><img src="'.$url.'" style="width:150px;max-height:150px">
                        <a href="javascript:;" onclick="deleteImage('.$id.')" style="color:red">delete</a></td>
                    </tr>';
        }
        if (!$tr) $tr='<tr><td>no images</td></tr>';
        $result['tr'] = $tr;
        $this->sendJson($result);
    }
}