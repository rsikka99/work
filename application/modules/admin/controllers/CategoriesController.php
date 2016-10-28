<?php
use Tangent\Controller\Action;

/**
 * Class Admin_IndexController
 */
class Admin_CategoriesController extends Action
{

    public function indexAction ()
    {
        $this->view->error=false;

        $db = Zend_Db_Table::getDefaultAdapter();
        if ($this->getParam('delete')) {
            $n = $db->query('select count(*) from base_product where categoryId='.intval($this->getParam('delete')))->fetchColumn(0);
            if ($n>0) {
                $this->view->error="Cannot delete this category because it contains products.";
            } else {
                $db->query('DELETE FROM base_category WHERE id=' . intval($this->getParam('delete')));
                header('Location: /admin/categories');
                exit();
            }
        }

        $this->_pageTitle = ['Catalog Categories'];

        $this->view->base_category = $db->query('select * from base_category order by name')->fetchAll();
    }

    public function addAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->base_category = $db->query('select * from base_category order by name')->fetchAll();

        if ($this->getRequest()->getMethod()=='POST') {
            if ($this->getParam('id')) {
                $id = max(1,intval($this->getParam('id')));
                $e = $db->query('select id from base_category where id='.$id)->fetchColumn(0);
                if (!$e) {
                    $st = $db->prepare('INSERT INTO base_category SET id=?, name=?, parent=?');
                    $st->execute([$id, $this->getParam('name'), $this->getParam('parent') ? $this->getParam('parent') : null]);
                }
            } else {
                $st = $db->prepare('INSERT INTO base_category SET name=?, parent=?');
                $st->execute([$this->getParam('name'), $this->getParam('parent') ? $this->getParam('parent') : null]);
            }
            header('Location: /admin/categories');
            exit();
        }

        $this->_pageTitle = ['Add Category'];
    }

    public function editAction() {
        $db = Zend_Db_Table::getDefaultAdapter();

        if ($this->getRequest()->getMethod()=='POST') {
            $st = $db->prepare('update base_category set name=?, parent=?, properties=? where id=?');
            $st->execute([$this->getParam('name'), $this->getParam('parent')?$this->getParam('parent'):null, $this->getParam('properties'), $this->getParam('id')]);
            header('Location: /admin/categories');
            exit();
        }

        $this->view->base_category = $db->query('select * from base_category order by name')->fetchAll();
        $this->view->edit = $db->query('select * from base_category where id='.intval($this->getParam('id')))->fetch();

        $this->_pageTitle = ['Edit Category'];
    }
}

