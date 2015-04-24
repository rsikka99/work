<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;
use Twitter_Bootstrap_Form_Element_Submit;
use Twitter_Bootstrap_Form_Inline;
use Zend_Form_Element_Select;

/**
 * Class SelectQuoteForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class SelectQuoteForm extends Twitter_Bootstrap_Form_Inline
{
    /**
     * The user id to get quotes for.
     *
     * @var int
     */
    protected $_userId;

    /**
     * @param null|int   $userId
     * @param null|array $options
     */
    public function __construct ($userId, $options = null)
    {
        $this->_userId = $userId;
        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)    Stacked, left-aligned labels
         * over controls (default)
         * - Inline .form-inline Left-aligned label and inline-block controls
         * for compact style
         * - Search .form-search Extra-rounded text input for a typical search
         * aesthetic
         * - Horizontal .form-horizontal
         *
         * Use .form-horizontal to have same experience as with Bootstrap v1!
         */
        $this->_addClassNames('form-center-actions');

        $clientList = [];
        /* @var $client ClientModel */
        foreach (ClientMapper::getInstance()->fetchAll() as $client)
        {
            $clientList [$client->id] = $client->companyName;
        }
        $clientSelect = new Zend_Form_Element_Select('clientId');
        $clientSelect->addMultiOptions($clientList);
        $clientSelect->setLabel('Company Name');
        $this->addElement($clientSelect);

        $quoteList          = [];
        $quoteListValidator = [];
        /* @var $quote QuoteModel */
        foreach (QuoteMapper::getInstance()->fetchAllForUser($this->_userId) as $quote)
        {
            $clientName             = $quote->getClient()->companyName;
            $dateCreated            = $quote->dateCreated;
            $quoteList [$quote->id] = "$clientName - $dateCreated";
            $quoteListValidator []  = $quote->id;
        }

        // Create a select element to hold quotes for the user
        $quotes = new Zend_Form_Element_Select('quoteId');

        // Quotes element setup vars
        $quotes->addMultiOptions($quoteList);
        $quotes->addValidator('InArray', false, [
            $quoteListValidator,
        ]);
        $quotes->setLabel('Quote Date');

        // Add the quote element to the form
        $this->addElement($quotes);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'ignore'     => true,
            'label'      => 'Continue',
            'decorators' => [
                'ViewHelper',
                [
                    'HtmlTag',
                    [
                        'tag'   => 'div',
                        'class' => 'form-actions'
                    ],
                ],
            ],
        ]);
    }
}
