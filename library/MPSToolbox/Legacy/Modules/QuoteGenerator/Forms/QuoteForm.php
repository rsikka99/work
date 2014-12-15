<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use Twitter_Bootstrap_Form_Horizontal;

/**
 * Class QuoteForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteForm extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * @var int
     */
    protected $_leasingSchemaId;

    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);
        QuoteNavigationForm::addFormActionsToForm(QuoteNavigationForm::BUTTONS_SAVE, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $clientList          = array();
        $clientListValidator = array();

        foreach (ClientMapper::getInstance()->fetchAll() as $client)
        {
            $clientList [$client->id] = $client->companyName;
            $clientListValidator []   = $client->id;
        }

        $clients = $this->createElement('select', 'clientId');
        $clients->setLabel('Select Client:');
        $clients->addMultiOptions($clientList);
        $clients->addValidator('InArray', false, array(
            $clientListValidator
        ));
        $this->addElement($clients);

        $this->addElement('radio', 'quoteType', array(
            'label'        => 'Type Of Quote:',
            'filters'      => array(
                'StringTrim',
                'StripTags'
            ),
            'multiOptions' => array(
                'purchased' => 'Purchased',
                'leased'    => 'Leased'
            ),
            'required'     => true
        ));

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'index/form/quote.phtml'
                )
            )
        ));
    }
}