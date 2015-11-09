<?php

use \MPSToolbox\Entities\RmsUpdateEntity;
use \MPSToolbox\Entities\TonerColorEntity;

class Default_CronController extends \Tangent\Controller\Action {

    public function rmsUpdateAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();


        $service = new \MPSToolbox\Services\RmsUpdateService();

        $clients = $service->getRmsClients();
        foreach ($clients as $client) {
            if (empty($client['deviceGroup'])) continue;
            $newDeviceNeedsToner = false;
            $devices = $service->update($client['clientId'], $client['rmsUri'], $client['deviceGroup']);
            foreach ($devices as $device) {
                /** @var RmsUpdateEntity $device */

                $meter=$device->getEndMeterBlack() - $device->getStartMeterBlack();
                $diff = date_diff($device->getMonitorStartDate(), $device->getMonitorEndDate());
                $daily = $meter/$diff->days;
                if ($device->needsToner(TonerColorEntity::BLACK, $daily)) {
                    $service->deviceNeedsToner($device, $client, TonerColorEntity::BLACK);
                } else if ($device->getTonerLevelBlack()>5) {
                    $service->tonerMayBeReplaced($device, TonerColorEntity::BLACK);
                }

                $meter=$device->getEndMeterColor() - $device->getStartMeterColor();
                $diff = date_diff($device->getMonitorStartDate(), $device->getMonitorEndDate());
                $daily = $meter/$diff->days;
                if ($device->needsToner(TonerColorEntity::MAGENTA, $daily)) {
                    $newDeviceNeedsToner |= $service->deviceNeedsToner($device, $client, TonerColorEntity::MAGENTA);
                } else if ($device->getTonerLevelMagenta()>5) {
                    $service->tonerMayBeReplaced($device, TonerColorEntity::MAGENTA);
                }
                if ($device->needsToner(TonerColorEntity::CYAN, $daily)) {
                    $newDeviceNeedsToner |= $service->deviceNeedsToner($device, $client, TonerColorEntity::CYAN);
                } else if ($device->getTonerLevelCyan()>5) {
                    $service->tonerMayBeReplaced($device, TonerColorEntity::CYAN);
                }
                if ($device->needsToner(TonerColorEntity::YELLOW, $daily)) {
                    $newDeviceNeedsToner |= $service->deviceNeedsToner($device, $client, TonerColorEntity::YELLOW);
                } else if ($device->getTonerLevelYellow()>5) {
                    $service->tonerMayBeReplaced($device, TonerColorEntity::YELLOW);
                }
            }

            if ($newDeviceNeedsToner) {
                printf('sending email...');
                $service->sendEmail($client);
            }
        }
    }

}

