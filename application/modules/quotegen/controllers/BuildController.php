<?php

class Quotegen_BuildController extends Zend_Controller_Action
{
    
    /**
     * The quote model.
     * We should always have a quote id in the session when we are in this controller.
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;
    
    /**
     * The id of our current quote.
     *
     * @var number
     */
    protected $_quoteId;
    
    /**
     * The quotegen session namespace.
     *
     * @var Zend_Session_Namespace
     */
    protected $_quoteSession;

    public function init ()
    {
        try
        {
            $this->_quoteSession = new Zend_Session_Namespace(Quotegen_Model_Quote::QUOTE_SESSION_NAMESPACE);
            
            // If we do not have a quote id, then we should be sent back to our main page
            if (! isset($this->_quoteSession->id))
            {
                throw new Exception('No quote id selected');
            }
            $this->_quoteId = $this->_quoteSession->id;
            
            // Fetch our quote
            $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->_quoteId);
            if (! $this->_quote)
            {
                throw new Exception("Quote with the id of {$this->_quoteId} was not found.");
            }
            
            $this->view->quote = $this->_quote;
        }
        catch ( Exception $e )
        {
            $this->_helper->flashMessenger(array (
                    'danger' => "There was an error preparing your quote. Please try again." 
            ));
            throw new Exception($e);
            $this->_helper->redirector('index', 'index');
        }
    }

    /**
     * The index action is for the main page of building a quote.
     * If we don't have a quote here then we should be sent to the "new" page.
     */
    public function indexAction ()
    {
    }

    /**
     * This handles creating a new quote only.
     */
    public function newAction ()
    {
    }
}

