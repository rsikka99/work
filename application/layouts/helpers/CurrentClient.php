<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;

/**
 * App_View_Helper_CurrentClient
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_CurrentClient extends Zend_View_Helper_Abstract
{
    static $_currentClient;
    static $_mpsSession;
    static $_identity;

    /**
     * Gets the currently selected client
     *
     * @return ClientModel|bool
     */
    public function CurrentClient ()
    {
        if (!isset(self::$_currentClient))
        {
            if (!isset(self::$_mpsSession))
            {
                self::$_mpsSession = new Zend_Session_Namespace('mps-tools');
            }

            if (!isset(self::$_identity))
            {
                self::$_identity = (Zend_Auth::getInstance()->hasIdentity()) ? Zend_Auth::getInstance()->getIdentity() : false;
            }

            if (self::$_identity !== false && isset(self::$_mpsSession->selectedClientId))
            {
                $client = ClientMapper::getInstance()->find(self::$_mpsSession->selectedClientId);
                if ($client instanceof ClientModel && $client->dealerId == self::$_identity->dealerId)
                {
                    self::$_currentClient = $client;
                }
                else
                {
                    unset(self::$_mpsSession->selectedClientId);
                }
            }
        }

        return self::$_currentClient;
    }
}
