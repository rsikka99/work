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
     * Creates a quoteSetting
     */
    public function createAction ()
    {
        $form = new Quotegen_Form_QuoteSetting();
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Save to the database
                        $quoteSettings = new Quotegen_Model_Quotesetting();
                        $quoteSettings->populate($values);
                        Quotegen_Model_Mapper_QuoteSetting::getInstance()->insert($quoteSettings);
                        $this->_helper->redirector('index');
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
                            'danger' => 'Error creating user.  Please try again.' 
                    ));
                }
            }
            else
            {
                // Redirect back to the homepage
                $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * Deletes a quoteSetting
     */
    public function deleteAction ()
    {
        // Get the passed id
        $quoteSettingId = $this->_getParam('id', false);
        
        // Redirect if now id exists
        if (! $quoteSettingId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'No quoteSetting was chosen to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Find quoteSetting
        $quoteSettingMapper = Quotegen_Model_Mapper_QuoteSetting::getInstance();
        $quoteSetting = $quoteSettingMapper->find($quoteSettingId);
        
        // Return to index if not found
        if (! $quoteSetting)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'There was an error finding that quoteSetting. Please try again.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Show delete form
        $form = new Application_Form_Delete('Are you sure you want to delete this quote setting ?');
        
        // If post and valid delete option
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Attempt to delete user 
                        $quoteSettingMapper->delete($quoteSetting);
                        
                        // Redirect and show message
                        $this->_helper->flashMessenger(array (
                                'success' => 'Quote setting was deleted succesfully.' 
                        ));
                        $this->_helper->redirector('index');
                    }
                }
                catch ( Exception $e )
                {
                    // Delete was unsuccesfull
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Delete was unsuccessful please try again.' 
                    ));
                }
            }
            else // if cancel is hit return to index
            {
                $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
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

