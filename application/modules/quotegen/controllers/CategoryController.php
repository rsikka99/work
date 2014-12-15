<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\CategoryForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionCategoryMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\CategoryMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CategoryModel;
use Tangent\Controller\Action;

/**
 * Class Quotegen_CategoryController
 */
class Quotegen_CategoryController extends Action
{

    public function init ()
    {
    }

    // Shows all the current categories in a table
    public function indexAction ()
    {
        // Get all current items in categories table
        $categoryMapper = new CategoryMapper();
        $paginator      = new Zend_Paginator(new My_Paginator_MapperAdapter($categoryMapper, array('dealerId = ?' => Zend_Auth::getInstance()->getIdentity()->dealerId)));

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

        if (!$categoryId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select a category to delete first.'
            ));
            $this->redirectToRoute('quotes.category-options');
        }

        // Get a MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CategoryModel object from the id that was passed.
        $category = CategoryMapper::getInstance()->find($categoryId);

        if (!$category)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'There was an error selecting the category to delete.'
            ));
            $this->redirectToRoute('quotes.category-options');
        }
        // If we are trying to access a category from another dealer, kick them back to index
        if ($category && $category->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'You do not have permission to access this.'
            ));
            // Redirect
            $this->redirectToRoute('quotes.category-options');
        }
        $message = "Are you sure you want to delete {$category->name}?";
        $form    = new DeleteConfirmationForm($message);
        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Delete the entry from the lookup table by the category id.
                        OptionCategoryMapper::getInstance()->deleteByCategoryId($categoryId);
                        // Delete the entry from the category table.
                        CategoryMapper::getInstance()->delete($category);

                        $this->_flashMessenger->addMessage(array(
                            'success' => "Category  {$this->view->escape($category->name)} was deleted successfully."
                        ));

                        // Redirect the user back to index action of this controller.
                        $this->redirectToRoute('quotes.category-options');
                    }
                }
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => 'There was an error deleting this category.  Please try again.'
                    ));
                }
            }
            else
            {
                // Redirect the user back to index action of this controller.
                $this->redirectToRoute('quotes.category-options');
            }
        }
        $this->view->form = $form;
    }

    public function createAction ()
    {
        $form    = new CategoryForm();
        $request = $this->getRequest();

        if ($request->isPost())
        {
            // When user is posting data, get the values that have been posted.
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Attempt to save the category to the database.
                        $category           = new CategoryModel();
                        $category->dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        $category->populate($values);
                        CategoryMapper::getInstance()->insert($category);

                        // Redirect client back to index
                        $this->redirectToRoute('quotes.category-options');
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'Please correct the errors below.'
                        ));
                    }
                }
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => 'There was an error creating this category.  Please try again..'
                    ));
                }
            }
            else // If user has selected cancel send user back to the index pages of this Controller
            {
                $this->redirectToRoute('quotes.category-options');
            }
        }
        $this->view->form = $form;
    }

    public function editAction ()
    {
        $categoryId = $this->_getParam('id', false);

        if (!$categoryId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select a category first.'
            ));
            $this->redirectToRoute('quotes.category-options');
        }

        // Find client and pass form object
        $form     = new CategoryForm();
        $category = CategoryMapper::getInstance()->find($categoryId);

        // If we are trying to access a category from another dealer, kick them back to index
        if ($category && $category->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'You do not have permission to access this.'
            ));
            // Redirect
            $this->redirectToRoute('quotes.category-options');
        }
        $form->populate($category->toArray());

        $request = $this->getRequest();
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
                        $category->populate($values);
                        $category->id = $categoryId;
                        CategoryMapper::getInstance()->save($category, $categoryId);

                        $this->_flashMessenger->addMessage(array(
                            'success' => "Category was updated successfully."
                        ));

                        $this->redirectToRoute('quotes.category-options');
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => 'Please correct the errors below'
                        ));
                    }
                }
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => 'There was an error updating this category.  Please try again'
                    ));
                }
            }
            else
            {
                // User has cancelled - redirect
                $this->redirectToRoute('quotes.category-options');
            }
        }
        $this->view->form = $form;
    }

    public function viewAction ()
    {
        $categoryId = $this->_getParam('id', false);

        // Get the client
        $mapper   = new CategoryMapper();
        $category = $mapper->find($categoryId);
        // If we are trying to access a category from another dealer, kick them back to index
        if ($category && $category->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'You do not have permission to access this.'
            ));
            // Redirect
            $this->redirectToRoute('quotes.category-options');
        }
        $this->view->category = $category;
    }
}

