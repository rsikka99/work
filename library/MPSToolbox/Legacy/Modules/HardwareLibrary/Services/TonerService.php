<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Services;

use MPSToolbox\Entities\DealerEntity;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\TonerEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceTonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceTonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use Tangent\Accounting;
use Zend_Auth;
use Zend_Db_Expr;

/**
 * Class TonerService
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Services
 */
class TonerService
{
    /**
     * @var bool
     */
    protected $isMasterHardwareAdministrator;

    /**
     * @var int
     */
    protected $dealerId;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @param int  $userId
     * @param int  $dealerId
     * @param bool $isMasterHardwareAdministrator
     */
    public function __construct ($userId=null, $dealerId=null, $isMasterHardwareAdministrator=false)
    {
        if (!$dealerId) $dealerId = DealerEntity::getDealerId();

        $this->userId                        = $userId;
        $this->dealerId                      = $dealerId;
        $this->isMasterHardwareAdministrator = $isMasterHardwareAdministrator;
    }

    /**
     * @param TonerModel $toner
     * @param array      $data
     */
    public function saveDealerAttributes ($toner, $data)
    {

        $dealerTonerAttributesMapper = DealerTonerAttributeMapper::getInstance();

        // Save to dealer Toner Attributes
        $dealerTonerAttributes = $dealerTonerAttributesMapper->findTonerAttributeByTonerId($toner->id, $this->dealerId);

        if ($dealerTonerAttributes instanceof DealerTonerAttributeModel)
        {
            // This allows null to be saved to the database.
            $dealerTonerAttributes->cost      = ($data['dealerCost'] == '' ? new Zend_Db_Expr("NULL") : $data['dealerCost']);
            $dealerTonerAttributes->dealerSku = ($data['dealerSku'] == '' ? new Zend_Db_Expr("NULL") : $data['dealerSku']);

            // If these are NULL we want to remove it from the database
            if ($dealerTonerAttributes->cost == new Zend_Db_Expr("NULL") && $dealerTonerAttributes->dealerSku == new Zend_Db_Expr("NULL"))
            {
                $dealerTonerAttributesMapper->delete($dealerTonerAttributes);
            }
            // At least one is not null, lets save it
            else
            {
                $dealerTonerAttributesMapper->save($dealerTonerAttributes);
            }
        }
        else
        {
            $dealerTonerAttributes            = new DealerTonerAttributeModel();
            $dealerTonerAttributes->tonerId   = $toner->id;
            $dealerTonerAttributes->dealerId  = $this->dealerId;
            $dealerTonerAttributes->cost      = ($data['dealerCost'] == '' ? new Zend_Db_Expr("NULL") : $data['dealerCost']);
            $dealerTonerAttributes->dealerSku = ($data['dealerSku'] == '' ? new Zend_Db_Expr("NULL") : $data['dealerSku']);
            $dealerTonerAttributesMapper->insert($dealerTonerAttributes);
        }
    }

    /**
     * Creates a toner model
     *
     * @param array $data
     *
     * @return TonerModel
     */
    public function createToner ($data)
    {
        $tonerMapper = TonerMapper::getInstance();
        $toner       = new TonerModel($data);

        if ($this->isMasterHardwareAdministrator)
        {
            $toner->isSystemDevice = 1;
        }
        else
        {
            $toner->isSystemDevice = 0;
            $toner->cost = static::obfuscateTonerCost($toner->cost);
        }

        $toner->userId = $this->userId;
        $tonerMapper->insert($toner);

        if ($data['imageUrl']) {
            $this->downloadImageFromImageUrl($toner, $data['imageUrl']);
            $tonerMapper->save($toner);
        }

        $st=\Zend_Db_Table::getDefaultAdapter()->prepare('update ingram_products set tonerId=:tonerId where tonerId is null and `vendor_part_number`=:vpn');
        $st->execute(['tonerId'=>$toner->id, 'vpn'=>$data['sku']]);

        $st=\Zend_Db_Table::getDefaultAdapter()->prepare("update ingram_products set tonerId=:tonerId where tonerId is null and `vendor_part_number` like :like");
        $st->execute(['tonerId'=>$toner->id, 'like'=>"{$data['sku']}#%"]);

        if (!$data['weight']) {
            $st = \Zend_Db_Table::getDefaultAdapter()->prepare('UPDATE toners t SET weight = 0.453592 * (select weight from ingram_products where tonerId=t.id) WHERE id=:tonerId');
            $st->execute(['tonerId'=>$toner->id]);
        }
        if (!$data['upc']) {
            $st = \Zend_Db_Table::getDefaultAdapter()->prepare('UPDATE toners t SET upc = (select upc_code from ingram_products where tonerId=t.id) WHERE id=:tonerId');
            $st->execute(['tonerId'=>$toner->id]);
        }

        return $toner;
    }

    /**
     * Deletes a toner
     *
     * @param int $tonerId
     */
    public function deleteToner ($tonerId)
    {
        $toner = TonerMapper::getInstance()->find($tonerId);
        if ($toner instanceof TonerModel)
        {
            TonerMapper::getInstance()->delete($tonerId);
            TonerVendorManufacturerMapper::getInstance()->updateTonerVendorByManufacturerId($toner->manufacturerId);
        }
    }

    /**
     * Finds a toner
     *
     * @param int $tonerId
     *
     * @return TonerModel
     */
    public function findToner ($tonerId)
    {
        return TonerMapper::getInstance()->find($tonerId);
    }

    public function updateToner ($tonerId, $data)
    {
        $tonerMapper = TonerMapper::getInstance();
        $toner       = $tonerMapper->find($tonerId);

        if ($toner instanceof TonerModel)
        {
            if ($this->isMasterHardwareAdministrator || $toner->isSystemDevice == 0)
            {
                if ($data['imageUrl'] && (0!==strcmp($data['imageUrl'], $toner->imageUrl))) {
                    $this->downloadImageFromImageUrl($toner, $data['imageUrl']);
                } else {
                    $data['imageUrl'] = $toner->imageUrl;
                }

                $toner->sku            = $data['sku'];
                $toner->cost           = $data['cost'];
                $toner->yield          = $data['yield'];
                $toner->tonerColorId   = $data['tonerColorId'];
                $toner->manufacturerId = $data['manufacturerId'];
                $toner->imageUrl       = $data['imageUrl'];
                $toner->name           = $data['name'];
                $toner->weight         = $data['weight'];
                $toner->UPC            = $data['UPC'];

                if ($data['saveAndApproveHdn'] == 1)
                {
                    $toner->isSystemDevice = 1;
                }

                $tonerMapper->save($toner);
            }

            return $toner;
        }

        return false;
    }

    public function uploadImage(TonerModel $toner, $upload) {
        $publicFilePath = '/img/toners/'.$upload['name'];
        $tmpFilePath       = PUBLIC_PATH . $publicFilePath;
        move_uploaded_file($upload['tmp_name'], $tmpFilePath);

        $image_info = @getimagesize($tmpFilePath);
        if (!$image_info || ($image_info[0]<1)) {
            unlink($tmpFilePath);
            return;
        }
        $ext=null;
        switch ($image_info['mime']) {
            case 'image/jpeg' :
                $ext='jpg';
                break;
            case 'image/png' :
                $ext='png';
                break;
            case 'image/gif' :
                $ext='gif';
                break;
        }
        if (!$ext) {
            unlink($tmpFilePath);
            return;
        }

        if ($toner->imageFile) {
            $publicFilePath = '/img/toners/'.$toner->imageFile;
            $filePath       = PUBLIC_PATH . $publicFilePath;
            unlink($filePath);
        }

        $toner->imageFile = $toner->id.'_'.time().'.'.$ext;
        $publicFilePath = '/img/toners/'.$toner->imageFile;
        $filePath       = PUBLIC_PATH . $publicFilePath;
        rename($tmpFilePath, $filePath);
    }

    public function downloadImageFromImageUrl(TonerModel $toner, $url=null) {
        if (!$url) $url = $toner->imageUrl;
        if (!$url) return;
        $image_info = @getimagesize($url);
        if (!$image_info || ($image_info[0]<1)) return;
        $ext=null;
        switch ($image_info['mime']) {
            case 'image/jpeg' :
                $ext='jpg';
                break;
            case 'image/png' :
                $ext='png';
                break;
            case 'image/gif' :
                $ext='gif';
                break;
        }
        if (!$ext) return;

        if ($toner->imageFile) {
            $publicFilePath = '/img/toners/'.$toner->imageFile;
            $filePath       = PUBLIC_PATH . $publicFilePath;
            unlink($filePath);
        }

        $toner->imageFile = $toner->id.'_'.time().'.'.$ext;
        $publicFilePath = '/img/toners/'.$toner->imageFile;
        $filePath       = PUBLIC_PATH . $publicFilePath;
        file_put_contents($filePath, file_get_contents($url));
    }

    /**
     * Handles mapping a toner to a device
     *
     * @param int $tonerId
     * @param int $masterDeviceId
     */
    public function mapToner ($tonerId, $masterDeviceId)
    {
        $masterDevice      = MasterDeviceMapper::getInstance()->find($masterDeviceId);
        $toner             = TonerMapper::getInstance()->find($tonerId);
        $deviceTonerMapper = DeviceTonerMapper::getInstance();

        /**
         * Map The toner
         */
        if ($masterDevice instanceof MasterDeviceModel && $toner instanceof TonerModel)
        {
            $deviceToner = $deviceTonerMapper->find([$toner->id, $masterDevice->id]);
            if (!$deviceToner instanceof DeviceTonerModel)
            {
                $deviceToner                   = new DeviceTonerModel();
                $deviceToner->master_device_id = $masterDevice->id;
                $deviceToner->toner_id         = $toner->id;

                $deviceToner->isSystemDevice = $this->isMasterHardwareAdministrator;

                $deviceTonerMapper->insert($deviceToner);
            }
        }
    }

    /**
     * Handles unmapping a toner to a device
     *
     * @param int $tonerId
     * @param int $masterDeviceId
     */
    public function unmapToner ($tonerId, $masterDeviceId)
    {
        $deviceToner                   = new DevicetonerModel();
        $deviceToner->toner_id         = $tonerId;
        $deviceToner->master_device_id = $masterDeviceId;
        DeviceTonerMapper::getInstance()->delete($deviceToner);
    }

    /**
     * Returns a list of toners by their color id for manipulation
     *
     * @param TonerModel[] $toners
     *
     * @return TonerModel[]
     */
    public function getTonersByColorId ($toners)
    {
        $tonersByColorId = [];
        foreach ($toners as $toner)
        {
            $tonersByColorId [$toner->tonerColorId] = $toner;
        }

        return $tonersByColorId;
    }

    /**
     * Returns an array of toners based on OEM manufacturer
     *
     * @param $tonerSets
     *
     * @return TonerModel[]
     */
    public function getOemTonersByTonerSet ($tonerSets)
    {
        $oemTonerArray = [];

        foreach ($tonerSets as $tonerSet)
        {
            if ($tonerSet['isOem'])
            {
                $oemTonerArray = $tonerSet['toners'];
            }
        }

        return $oemTonerArray;
    }

    /**
     * Takes in a toner cost and applies a 5 - 10 % margin on the cost.
     *
     * @param float $cost
     *
     * @return float
     */
    public static function obfuscateTonerCost ($cost)
    {
        return round(Accounting::applyMargin($cost, rand(5, 10)));
    }

    /**
     * Fetches a list of toner entities
     *
     * @param array $tonerIds
     *
     * @return TonerEntity[]
     */
    public function getToners ($tonerIds)
    {
        $toners = [];

        $tonerEntities = TonerEntity::find($tonerIds);
        if ($tonerEntities instanceof TonerEntity)
        {
            $toners[] = $tonerEntities;
        }
        else
        {
            foreach ($tonerEntities as $tonerEntity)
            {
                $toners[] = $tonerEntity;
            }
        }

        return $toners;
    }

    public function getTonerPrice($tonerId) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->query(
"
select a.cost, ingram_prices.customer_price as ingramPrice
from toners t
  left join dealer_toner_attributes a on t.id=a.tonerId and a.dealerId={$this->dealerId}
  left join ingram_products on ingram_products.tonerId = t.id
  left join ingram_prices on ingram_prices.ingram_part_number = ingram_products.ingram_part_number
where t.id = ".intval($tonerId)
        );
        $line = $st->fetch();
        if ($line['ingramPrice'] && (!$line['cost'] || ($line['ingramPrice']<$line['cost']))) $line['cost'] = $line['ingramPrice'];
        return $line['cost'];
    }

}