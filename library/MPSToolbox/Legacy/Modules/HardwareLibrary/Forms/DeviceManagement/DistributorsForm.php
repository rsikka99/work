<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use MPSToolbox\Entities\DealerEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use My_Brand;
use My_Validate_DateTime;
use Zend_Form;

/**
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class DistributorsForm extends \My_Form_Form
{
    public function __construct ($options = null, $isAllowedToEditFields = false)
    {
        parent::__construct($options);
    }

    public function init ()
    {
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/distributors-form.phtml']]]);
    }

    public static function getDistributors($masterDevice) {
        /** @var MasterDeviceModel $masterDevice */
        $distributors=[];
        #--
        if ($masterDevice) {
            $dealerId = \Zend_Auth::getInstance()->getIdentity()->dealerId;
            $device = DeviceMapper::getInstance()->find([$masterDevice->id, $dealerId]);
            if ($device) {
                $dealer = \MPSToolbox\Legacy\Mappers\DealerMapper::getInstance()->find($dealerId);
                $distributors[] = [
                    'name' => /* $attr->distributor ? $attr->distributor : */ $dealer->dealerName,
                    'sku' => $device->dealerSku,
                    'price' => $device->cost,
                    'stock' => '',
                ];
            }
        }
        #--
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select * from ingram_products p join ingram_prices c using (ingram_part_number) where masterDeviceId=:masterDeviceId');
        $st->execute(['masterDeviceId'=>$masterDevice->id]);
        foreach ($st->fetchAll() as $line) {
            $distributors[] = [
                'name'=>'Ingram Micro',
                'sku'=>$line['ingram_part_number'],
                'price'=>$line['customer_price'],
                'stock'=>$line['availability_flag'],
            ];
        }
        #--
        return $distributors;
    }

}