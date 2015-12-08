<?php

class Default_WebhookController extends \Tangent\Controller\Action {

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
                $fields = explode(',','id,clientId,created_at,number,total_price,subtotal_price,line_items,customer,raw');
                $sql = [];
                foreach ($fields as $key) $sql[]="`$key`=:{$key}";
                $db = Zend_Db_Table::getDefaultAdapter();
                $st = $db->prepare('replace'.' into shopify_orders set '.implode(',',$sql));
                $st->execute([
                    'id'=>$order['id'],
                    'clientId'=>$client->id,
                    'created_at'=>$order['created_at'],
                    'number'=>$order['number'],
                    'total_price'=>$order['total_price'],
                    'subtotal_price'=>$order['subtotal_price'],
                    'line_items'=>json_encode($order['line_items']),
                    'customer'=>json_encode($order['customer']),
                    'raw'=>$input,
                ]);

                foreach ($order['line_items'] as $line_item) {
                    $rmsDeviceInstanceId = null;
                    foreach ($line_item['properties'] as $key=>$property) {
                        if ($key=='Device') {
                            $e = explode('; ', $property);
                            if (!empty($e) && is_numeric($e[0])) {
                                $rmsDeviceInstanceId = $e[0];
                            }
                        }
                    }
                    if ($rmsDeviceInstanceId) {
                        $attr = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper::getInstance()->findTonerAttributeByWebId($line_item['id']);
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