<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;

/**
 * Class SelectRmsUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services
 */
class SelectRmsUploadService
{
    /**
     * The clients id
     *
     * @var int
     */
    protected $_clientId;

    /**
     * @var RmsUploadModel[]
     */
    protected $_rmsUploads;


    /**
     * @param int $clientId The id of the client
     */
    public function __construct ($clientId)
    {
        $this->_clientId = $clientId;
    }

    /**
     * Gets the rmsUploads available to the client
     *
     * @return RmsUploadModel[]
     */
    public function getRmsUploads ()
    {
        if (!isset($this->_rmsUploads))
        {
            $this->_rmsUploads = RmsUploadMapper::getInstance()->fetchAllForClient($this->_clientId);
        }

        return $this->_rmsUploads;
    }

    /**
     * Validates an incoming RMS upload id to match the selected client id
     *
     * @param $rmsUploadId
     *
     * @return bool|RmsUploadModel
     */
    public function validateRmsUploadId ($rmsUploadId)
    {
        $rmsUploadId = (int)$rmsUploadId;
        $rmsUpload   = RmsUploadMapper::getInstance()->find($rmsUploadId);
        if ($rmsUpload)
        {
            if ($rmsUpload->clientId == $this->_clientId)
            {
                return $rmsUpload;
            }
        }

        return false;
    }
}