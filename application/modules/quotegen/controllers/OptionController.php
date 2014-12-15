<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\OptionForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionCategoryMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionCategoryModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CategoryModel;
use Tangent\Controller\Action;

/**
 * Class Quotegen_OptionController
 */
class Quotegen_OptionController extends Action
{

    public function init ()
    {
    }

    public function indexAction ()
    {
        // Get all current items in categories table
        $optionMapper = OptionMapper::getInstance();
        $paginator    = new Zend_Paginator(new My_Paginator_MapperAdapter($optionMapper, array('dealerId = ?' => Zend_Auth::getInstance()->getIdentity()->dealerId)));

        // Set current page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set max items per page
        $paginator->setItemCountPerPage(25);

        // Save entries to view paginatior
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
        $optionId = $this->_getParam('id', false);

        if (!$optionId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select an option to delete first.'
            ));
            $this->redirectToRoute('quotes.category-options');
        }

        $optionMapper = OptionMapper::getInstance();
        $option       = $optionMapper->find($optionId);

        if (!$option)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'There was an error finding that option to delete.'
            ));
            $this->redirectToRoute('quotes.category-options');
        }
        // If we are trying to access a option from another dealer, kick them back
        else if ($option->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'You do not have permission to access this.'
            ));
            // Redirect
            $this->redirectToRoute('quotes.category-options');
        }

        $message = "Are you sure you want to delete {$option->name}?";
        $form    = new DeleteConfirmationForm($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                // Delete quote from database
                if ($form->isValid($values))
                {
                    OptionCategoryMapper::getInstance()->deleteByOptionId($option->id);
                    $optionMapper->delete($option);

                    $this->_flashMessenger->addMessage(array(
                        'success' => "Option  {$this->view->escape($option->name)} was deleted successfully."
                    ));
                    $this->redirectToRoute('quotes.category-options');
                }
            }
            else // Go back
            {
                $this->redirectToRoute('quotes.category-options');
            }
        }
        $this->view->form = $form;
    }

    public function createAction ()
    {
        // Get master device id if passed in
        $page = $this->_getParam('page', false);
        $id   = $this->_getParam('id', false);

        $form = new OptionForm();

        // If the form is on POST insert data
        $request = $this->getRequest();

        if ($request->isPost())
        {
            // Get values from the form
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        $optionMapper = OptionMapper::getInstance();

                        $option = new OptionModel();
                        $option->populate($values);
                        $option->dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        $optionId         = $optionMapper->insert($option);

                        // Create optionCategory with $optionId to save 
                        $optionCategory           = new OptionCategoryModel();
                        $optionCategory->optionId = $optionId;

                        foreach ($values ['categories'] as $categoryId)
                        {
                            $optionCategory->categoryId = $categoryId;
                            OptionCategoryMapper::getInstance()->insert($optionCategory);
                        }
                        $this->_flashMessenger->addMessage(array(
                            'success' => "Option {$values['name']} Created"
                        ));
                        if ($page == "options")
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirectToRoute('hardware-library.all-devices.options', array(
                                'id' => $id
                            ));
                        }
                        else
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirectToRoute('quotes.category-options');
                        }
                    }
                    else // Values in form data aren't valid.
                    {
                        throw new InvalidArgumentException('Please correct the fields below');
                    }
                }
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => $e->getMessage()
                    ));
                }
            }
            else // Cancel was hit: redirect user
            {
                if ($page == "options")
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('hardware-library.all-devices.options', array(
                        'id' => $id
                    ));
                }
                else
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirectToRoute('quotes.category-options');
                }
            }
        }
        // Add form to page
        $form->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/quotegen/option-form.phtml',
                )
            )
        ));

        $this->view->form = $form;
    }

    public function editAction ()
    {
        $optionId = $this->_getParam('id', false);

        // If not idea is set then back to index page
        if (!$optionId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select an option first.'
            ));
            // Redirect
            $this->redirectToRoute('quotes.category-options');
        }

        // Load the form for use
        $form = new OptionForm();

        // Find the option by id
        $optionMapper = OptionMapper::getInstance();
        $option       = $optionMapper->find($optionId);

        // If we are trying to access a option from another dealer, kick them back
        if ($option && $option->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'You do not have permission to access this.'
            ));
            // Redirect
            $this->redirectToRoute('quotes.category-options');
        }


        $optionValues = $option->toArray();
        /* @var $category CategoryModel */
        foreach ($option->getCategories() as $category)
        {
            $optionValues ['categories'] [] = $category->id;
        }

        $form->populate($optionValues);
        $request = $this->getRequest();

        // If request is on post update the record
        if ($request->isPost())
        {

            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Update quote setting and message to confirm
                        $option->populate($values);

                        $optionCategoryMapper = OptionCategoryMapper::getInstance();
                        // Create a new category since we know the option id will stay the same at all times.
                        $optionCategory           = new OptionCategoryModel();
                        $optionCategory->optionId = $option->id;

                        $categoryIds [] = array();
                        /* @var $category CategoryModel */
                        foreach ($option->getCategories() as $category)
                        {

                            if (array_search((string)$category->id, $values ['categories']) === false)
                            {
                                $optionCategory->categoryId = $category->id;
                                $optionCategoryMapper->delete($optionCategory);
                            }
                            else
                            {
                                $categoryIds [] = $category->id;
                            }
                        }

                        foreach ($values ['categories'] as $categoryPostId)
                        {
                            if (array_search($categoryPostId, $categoryIds) === false)
                            {
                                $optionCategory->categoryId = $categoryPostId;
                                $optionCategoryMapper->insert($optionCategory);
                            }
                        }
                        $optionMapper->save($option, $optionId);
                        $this->_flashMessenger->addMessage(array(
                            'success' => "Category setting was updated successfully."
                        ));

                        $this->redirectToRoute('quotes.category-options');
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => $e->getMessage()
                    ));
                }
            }
            else // Client hit cancel, redirect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirectToRoute('quotes.category-options');
            }
        }

        // Add form to page
        $form->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/quotegen/option-form.phtml',
                )
            )
        ));

        $this->view->form = $form;
    }

    public function viewAction ()
    {
        $this->view->option = OptionMapper::getInstance()->find($this->_getParam('id', false));
    }
}

