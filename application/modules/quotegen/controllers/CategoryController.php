
<?php

class Quotegen_CategoryController extends Zend_Controller_Action
{

    public function init ()
    {
    }
    
    // Shows all the current categories in a table
    public function indexAction ()
    {
        // Get all current items in categories table
        $categoryMapper = new Quotegen_Model_Mapper_Category();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($categoryMapper));
        
        // Set current page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set max items per page
        $paginator->setItemCountPerPage(25);
        
        // Save entries to view paginatior
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
        $categoryId = $this->_getParam('id', false);
        
        if (! $categoryId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a category to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $mapper = new Quotegen_Model_Mapper_Category();
        $category = $mapper->find($categoryId);
        
        if (! $category)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the category to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete {$category->getName()}?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete quote from database
                if ($form->isValid($values))
                {
                    Quotegen_Model_Mapper_OptionCategory::getInstance()->deleteByCategoryId($categoryId);
                    $mapper->delete($category);
                    
                    // TODO: Show deletion of options relations
                    $this->_helper->flashMessenger(array (
                            'success' => "Category  {$this->view->escape ( $category->getName() )} was deleted successfully." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
            else // go back
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function createAction ()
    {
        // Show the form 
        $form = new Quotegen_Form_Category();
        
        // If the form is on post insert data 
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            // Get values from the form
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Persist data to database
                        $categoryMapper = new Quotegen_Model_Mapper_Category();
                        $category = new Quotegen_Model_Category();
                        $category->populate($values);
                        
                        $categoryMapper->insert($category);
                        
                        // Redirect client back to index
                        $this->_helper->redirector('index');
                    }
                    else // Values in form data aren't valid. 
                    {
                        throw new InvalidArgumentException('Please correct the fields below');
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else // Cancel was hit: redirect user
            {
                $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }

    public function editAction ()
    {
        $categorySettingId = $this->_getParam('id', false);
        
        // If not idea is set then back to index page
        if (! $categorySettingId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a category first' 
            ));
            // Redirect
            $this->_helper->redirector('index');
        }
        
        // Find client and pass form object
        $form = new Quotegen_Form_Category();
        $mapper = new Quotegen_Model_Mapper_Category();
        $category = $mapper->find($categorySettingId);
        
        $form->populate($category->toArray());
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
                        // Update quotesetting and message to comfirm
                        $mapper = new Quotegen_Model_Mapper_Category();
                        $category = new Quotegen_Model_Category();
                        $category->populate($values);
                        $category->setId($categorySettingId);
                        
                        $mapper->save($category, $categorySettingId);
                        $this->_helper->flashMessenger(array (
                                'success' => "Category setting was updated sucessfully." 
                        ));
                        
                        $this->_helper->redirector('index');
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
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

    public function viewAction ()
    {
        $categoryId = $this->_getParam('id', false);
        
        // Get the client
        $mapper = new Quotegen_Model_Mapper_Category();
        $category = $mapper->find($categoryId);
        $this->view->category = $category;
    }
}

