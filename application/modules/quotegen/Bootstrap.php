<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\QuoteGeneratorAclModel;

/**
 * Class Quotegen_Bootstrap
 */
class Quotegen_Bootstrap extends Tangent\ModuleBootstrap
{
    /**
     * Sets the paginator view partials
     */
    protected function _initPaginatorViewPartial ()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('_partials/paginator.phtml');
    }

    /**
     * @return Zend_Loader_Autoloader_Resource
     */
    protected function _initLibraryAutoLoader ()
    {
        return $this->getResourceLoader()->addResourceType('library', 'library', 'library');
    }

    protected function _initAddToAcl ()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        if ($acl instanceof AppAclModel)
        {
            QuoteGeneratorAclModel::setupAcl($acl);
        }
    }
}

