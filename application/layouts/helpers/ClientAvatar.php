<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;

/**
 * Class App_View_Helper_ClientAvatar
 */
class App_View_Helper_ClientAvatar extends Zend_View_Helper_Abstract
{

    /**
     * @param ClientModel $client
     * @param int         $size
     *
     * @return string
     */
    public function ClientAvatar ($client, $size = 64)
    {
        $identicon = new \Identicon\Identicon();

        return sprintf('<img src="%1$s" width="%2$s" height="%2$s" alt="Client Avatar" />', $identicon->getImageDataUri($client->companyName, $size, 'FFFFFF'), $size);
    }
}
