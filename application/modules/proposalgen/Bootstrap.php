<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\ProposalgenAclModel;

/**
 * Class Proposalgen_Bootstrap
 */
class Proposalgen_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * @return Zend_Loader_Autoloader
     */
    protected function _initAutoLoader ()
    {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->registerNamespace('Custom_');
        $autoLoader->registerNamespace('Tangent_');
        $autoLoader->registerNamespace('gchart');

        return $autoLoader;
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
            ProposalgenAclModel::setupAcl($acl);
        }
    }
}