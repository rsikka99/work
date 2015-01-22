<?php

use MPSToolbox\Legacy\Entities\ClientEntity;
use MPSToolbox\Legacy\Entities\RmsUploadEntity;
use MPSToolbox\Legacy\Repositories\RmsUploadRepository;

class My_View_Helper_SelectedRmsUpload extends Zend_View_Helper_Abstract
{
    /**
     * @var RmsUploadEntity
     */
    protected $selectedRmsUpload;

    /**
     * Returns application settings
     */
    public function SelectedRmsUpload ()
    {
        if (!isset($this->selectedRmsUpload))
        {
            if ($this->view->MpsSession()->selectedRmsUploadId > 0)
            {
                $selectedClient = $this->view->SelectedClient();
                if ($selectedClient instanceof ClientEntity)
                {
                    $rmsUpload = RmsUploadRepository::find($this->view->MpsSession()->selectedRmsUploadId);
                    if ($rmsUpload instanceof RmsUploadEntity && $rmsUpload->clientId == $selectedClient->id)
                    {
                        $this->selectedRmsUpload = $rmsUpload;
                    }
                }
            }
        }

        return $this->selectedRmsUpload;
    }
}