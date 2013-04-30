<?php
/**
 * Class Proposalgen_Service_SelectRmsUpload
 */
class Proposalgen_Service_SelectRmsUpload
{
    /**
     * The clients id
     *
     * @var int
     */
    protected $_clientId;

    /**
     * @var Proposalgen_Model_Rms_Upload[]
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
     * Gets the RmsUploads available to the client
     *
     * @return Proposalgen_Model_Rms_Upload[]
     */
    public function getRmsUploads ()
    {
        if (!isset($this->_rmsUploads))
        {
            $this->_rmsUploads = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->fetchAllForClient($this->_clientId);
        }

        return $this->_rmsUploads;
    }

    /**
     * Validates an incoming rms upload id to match the selected client id
     *
     * @param $rmsUploadId
     *
     * @return bool|Proposalgen_Model_Rms_Upload
     */
    public function validateRmsUploadId ($rmsUploadId)
    {
        $rmsUploadId = (int)$rmsUploadId;
        $rmsUpload   = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($rmsUploadId);
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