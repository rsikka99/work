<?php

class Default_CronController extends \Tangent\Controller\Action {

    public function fmauditAction() {

        //set_time_limit(0);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $service = new \MPSToolbox\Services\RmsUpdateService();
        $service->updateFmaudit();
    }

    public function rmsUpdateAction() {
        set_time_limit(0);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $onlyClientId = $this->getParam('client');

        $service = new \MPSToolbox\Services\RmsUpdateService();

        $clients = $service->getRmsClients();
        $providers = $service->getDealerRmsProviders();

        foreach ($clients as $client) if (!empty($client['rmsUri'])) {
            if ($onlyClientId && ($onlyClientId!=$client['clientId'])) continue;

            echo "rms update {$client['clientId']}\n";

            $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings($client['dealerId']);
            $rmsProviderId = isset($providers[$client['dealerId']]) ? $providers[$client['dealerId']] : null;

            if (preg_match('#email\=fmaudit\@tangentmtw\.com#',$settings->shopSettings->rmsUri)) {
                $rmsProviderId = 8; //FM Audit 4.x
            }
            if (!empty($root) && preg_match('#^\w+-\w+-\w+-\w+-\w+$#', $root)) {
                $rmsProviderId = 6; //PrintFleet 3.x
            }

            switch ($rmsProviderId) {
                case 6: { // PrintFleet 3.x
                    if (!empty($client['deviceGroup'])) {
                        echo "updating PrintFleet client: {$client['clientId']}<br>\n";
                        $devices = $service->updateFromPrintfleet($client['clientId'], new \MPSToolbox\Api\PrintFleet($client['rmsUri']), $client['deviceGroup']);
                        $service->checkDevices($devices, $client, $settings->shopSettings);
                    }
                    break;
                }
                case 9: { // Lexmark
                    echo "updating Lexmark client: {$client['clientId']}<br>\n";
                    $url = parse_url($client['rmsUri']);
                    $lfm = new \MPSToolbox\Api\LFM($url);
                    $service->updateLfm($client['dealerId'], $lfm, $client['rmsGroup'], $client['deviceGroup']?$client['deviceGroup']:$client['companyName']);
                    break;
                }
            }
        }
    }

    public function distributorsAction() {
        set_time_limit(0);
        ini_set('memory_limit','512M');
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $service = new \MPSToolbox\Services\DistributorUpdateService();

        $dealerSuppliers = $service->getDealerSuppliers();
        $requestedDealer = $this->getRequest()->getParam('dealerId');
        $requestedSupplier = $this->getRequest()->getParam('supplierId');
        $dealers = [];
        foreach ($dealerSuppliers as $dealerSupplier) {
            if ($requestedDealer && ($requestedDealer!=$dealerSupplier['dealerId'])) continue;
            if ($requestedSupplier && ($requestedSupplier!=$dealerSupplier['supplierId'])) continue;
            $service->updatePrices($dealerSupplier);
            $dealers[$dealerSupplier['dealerId']] = $dealerSupplier['dealerId'];
        }

        if (php_sapi_name() == "cli") {
            $_SERVER['HTTP_HOST'] = 'cli';
            if (!file_exists('c:/')) {
                $_SERVER['HTTP_HOST'] = 'staging.mpstoolbox.com';
            }
        }

        foreach ($dealers as $dealerId) {
            file_get_contents('http://proxy.mpstoolbox.com/shopify/dist_update.php?dealerId='.$dealerId.'&origin='.$_SERVER['HTTP_HOST']);
        }
    }

    public function incompleteAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        #--
        $db = Zend_Db_Table::getDefaultAdapter();
        $arr = $db->query('
select mfg.fullName, count(*) as c from master_devices m join manufacturers mfg on m.manufacturerId=mfg.id where m.id not in (
select master_device_id
  from device_toners dt
    join master_devices msub on dt.master_device_id=msub.id
    join toners t on dt.toner_id=t.id and t.manufacturerId = msub.manufacturerId
)
or m.id not in (
  select baseProductId from cloud_file
)
or (isA3=0 and isAccessCard=0 and isADF=0 and isBinding=0 and isCapableOfReportingTonerLevels=0 and isDuplex=0 and isFax=0 and isPIN=0 and isSmartphone=0 and isStapling=0 and isTouchscreen=0 and isUSB=0 and isWalkup=0 and isWired=0 and isWireless=0)
group by manufacturerId
order by fullName')->fetchAll();

        if (!empty($arr)) {

            $total=0;
            foreach ($arr as $line) $total += $line['c'];

            $html =
"
<p>Hello,</p>
<p>There are currently {$c} incomplete printer models.</p>
<p>Here is the break down per manufacturer:</p>
<table>
";
            foreach ($arr as $line) {
                $html .= '<tr><td>'.$line['fullName'].'</td><td>'.$line['c'].'</td></tr>';
            }

            $html .= '</table>';
            $html .= '<p>Have a nice day!<br>MPS Toolbox</p>';

            $email = new \Zend_Mail();
            $email->setFrom('it@tangentmtw.com');
            $email->addTo('root@tangentmtw.com');
            $email->setSubject('Printer Model Notification');
            $email->setBodyHtml($html);
            $email->setBodyText(strip_tags($html));
            try {
                $email->send();
            } catch (\Exception $ex) {
                \Tangent\Logger\Logger::error($ex->getMessage(), $ex);
            }

        }

    }

}

