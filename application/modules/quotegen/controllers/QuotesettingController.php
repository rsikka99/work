<?php

class Quotegen_QuotesettingController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Displays all quoteSettings
     */
    public function indexAction ()
    {
        // Set up a quote setting mapper
        $mapper = Quotegen_Model_Mapper_QuoteSetting::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));
        
        // Set the current page that we are on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many display item
        $paginator->setItemCountPerPage(25);
        
        // Get all current quote settings
        $this->view->paginator = $paginator;
    }

    /**
     * Edits a quoteSetting
     */
    public function editAction ()
    {
        // Find client and pass form object
        $form = new Quotegen_Form_QuoteSetting(true);
        
        $quoteSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        
        $form->populate($quoteSetting->toArray());
        
        // update record if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        foreach ( $values as &$value )
                        {
                            if (strlen($value) === 0)
                            {
                                $value = new Zend_Db_Expr('NULL');
                            }
                        }
                        $quoteSetting->populate($values);
                        Quotegen_Model_Mapper_QuoteSetting::getInstance()->save($quoteSetting);
                        
                        // Rediret user with message
                        $this->_helper->flashMessenger(array (
                                'success' => "Quote setting was updated sucessfully." 
                        ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below.' 
                        ));
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Error editing quote setting.  Please try again.' 
                    ));
                }
            }
            else // Client hit cancel redicect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edits a quoteSetting
     */
    public function editdefaultAction ()
    {
        $quoteSettingId = 1;
        
        // Find client and pass form object
        $form = new Quotegen_Form_QuoteSetting();
        $quoteSettingMapper = Quotegen_Model_Mapper_QuoteSetting::getInstance();
        $quoteSetting = $quoteSettingMapper->find($quoteSettingId);
        
        $form->populate($quoteSetting->toArray());
        // update record if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Set nulls where needed.
                        foreach ( $values as &$value )
                        {
                            if (empty($value) && $value !== 0)
                            {
                                $value = new Zend_Db_Expr('NULL');
                            }
                        }
                        
                        // Update quoteSetting and message to comfirm
                        $quoteSetting->populate($values);
                        $quoteSetting->setId($quoteSettingId);
                        
                        $quoteSettingMapper->save($quoteSetting, $quoteSettingId);
                        $this->_helper->flashMessenger(array (
                                'success' => "Default quote setting was updated sucessfully." 
                        ));
                        
                        $this->_helper->redirector('editdefault');
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below' 
                        ));
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Error saving configuration.  Please try again.' 
                    ));
                }
            }
            else // Client hit cancel redicect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }
}

