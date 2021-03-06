<?php

class Default_WebhookController extends \Tangent\Controller\Action {

    public function printauditAction() {
        $service = new \MPSToolbox\Services\RmsRealtimeService();
        $str = file_get_contents('php://input');
        $xml = new SimpleXMLElement($str);
        $result = $service->processPrintauditXml($xml);
        $this->sendJson(["message" => "printaudit ".$result]);
    }

    public function shopifyOrderAction() {
        if (isset($_POST['input'])) {
            $input = $_POST['input'];
            file_put_contents(APPLICATION_BASE_PATH.'/data/logs/shopifyOrder.txt', $input);
        } else if (isset($_GET['test'])) {
            $input = file_get_contents(APPLICATION_BASE_PATH.'/data/logs/shopifyOrder.txt');
        } else {
            $input = file_get_contents('php://input');
            file_put_contents(APPLICATION_BASE_PATH.'/data/logs/shopifyOrder.txt', $input);
        }
        if (!$input) return;
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $order = json_decode($input, true);

        $dealerId = $this->getRequest()->getParam('dealerId');
        $dealer = \MPSToolbox\Legacy\Mappers\DealerMapper::getInstance()->find($dealerId);
        if ($dealer) {
            $clients = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->fetchClientListForDealer($dealerId);
            $client = null;
            foreach ($clients as $c) {
                if ($c->webId == $order['customer']['id']) {
                    $client = $c;
                }
            }
            if ($client) {
                $fields = explode(',','dealerId,id,clientId,created_at,number,total_price,subtotal_price,line_items,customer,customer_name,raw');
                $sql = [];
                foreach ($fields as $key) $sql[]="`$key`=:{$key}";
                $db = Zend_Db_Table::getDefaultAdapter();
                $st = $db->prepare('replace'.' into shopify_orders set '.implode(',',$sql));
                $data = [
                    'dealerId'=>$dealerId,
                    'id'=>$order['id'],
                    'clientId'=>$client->id,
                    'created_at'=>$order['created_at'],
                    'number'=>$order['number'],
                    'total_price'=>$order['total_price'],
                    'subtotal_price'=>$order['subtotal_price'],
                    'line_items'=>json_encode($order['line_items']),
                    'customer'=>json_encode($order['customer']),
                    'customer_name'=>$order['customer']['last_name'].', '.$order['customer']['first_name'],
                    'raw'=>$input,
                ];
                $st->execute($data);

                foreach ($order['line_items'] as $line_item) {
                    $rmsDeviceInstanceId = null;
                    foreach ($line_item['properties'] as $property) {
                        if ($property['name']=='Device') {
                            $e = explode('; ', $property['value']);
                            if (!empty($e) && is_numeric($e[0])) {
                                $rmsDeviceInstanceId = $e[0];
                            }
                        }
                    }
                    if ($rmsDeviceInstanceId) {
                        $attr = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper::getInstance()->findTonerAttributeByWebId($line_item['product_id']);
                        if ($attr) {
                            $st = $db->prepare('update device_needs_toner set shopify_order=:shopify_order where rmsDeviceInstanceId=:rmsDeviceInstanceId and toner=:toner');
                            $st->execute(['shopify_order'=>$order['id'], 'rmsDeviceInstanceId'=>$rmsDeviceInstanceId, 'toner'=>$attr->tonerId]);
                            echo 'Line Item '.$line_item['id'].'; rmsDeviceInstance '.$rmsDeviceInstanceId.'; toner '.$attr->tonerId.'<br>';
                        } else {
                            echo 'Line Item '.$line_item['id'].' could not be connected to a toner<br>';
                        }
                    } else {
                        echo 'Line Item '.$line_item['id'].' could not be connected to rmsDeviceInstance<br>';
                    }
                }
            } else {
                echo 'client not found: '.$order['customer']['email'];
            }
        } else {
            echo 'dealer not found: '.$dealerId;
        }
    }

}