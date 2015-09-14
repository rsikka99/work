<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use My_Brand;
use My_Validate_DateTime;
use Zend_Form;

/**
 * Class DeviceImageForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class HistoryForm extends \My_Form_Form
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
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/history-form.phtml']]]);
    }

    public static function getHistory($masterDevice) {
        /** @var MasterDeviceModel $masterDevice */
        if (empty($masterDevice)) return [];
        $dealerId = intval(\Zend_Auth::getInstance()->getIdentity()->dealerId);
        $db     = \Zend_Db_Table::getDefaultAdapter();
        $and = '';
        if ($dealerId!=1) {
            $and="and (dealerId is null or dealerId={$dealerId})";
        }
        $result = $db->fetchAssoc("SELECT * FROM `history` where `masterDeviceId`={$masterDevice->id} {$and} order by dt desc");
        $users = [];
        foreach ($db->fetchAssoc("SELECT * FROM users") as $line) $users[$line['id']]=sprintf("%s %s", $line['firstname'], $line['lastname']);
        foreach ($result as $i=>$line) {
            $result[$i]['user'] = isset($users[$line['userId']]) ? $users[$line['userId']] : '-';
            $result[$i]['date'] = date('d-m-Y H:i', strtotime($line['dt']));
        }
        return array_values($result);
    }

}