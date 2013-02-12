<?php
class Quotegen_Model_UserQuoteSetting extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $userId = 0;

    /**
     * @var int
     */
    public $quoteSettingId = 0;

    /**
     * Gets the related quoteSetting object
     *
     * @var Quotegen_Model_QuoteSetting
     */
    protected $_quoteSetting;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->quoteSettingId) && !is_null($params->quoteSettingId))
        {
            $this->quoteSettingId = $params->quoteSettingId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "userId"         => $this->userId,
            "quoteSettingId" => $this->quoteSettingId,
        );
    }

    /**
     * Gets the related QuoteSetting object for current userId
     *
     * @return the $_quoteSetting
     */
    public function getQuoteSetting ()
    {
        if (!isset($this->_quoteSetting))
        {
            $this->_quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->find($this->quoteSettingId);
        }

        return $this->_quoteSetting;
    }

    /**
     * Sets the related QuoteSetting object for current userId
     *
     * @param Quotegen_Model_QuoteSetting $_quoteSetting
     *            The new QuoteSetting object.
     */
    public function setQuoteSetting ($_quoteSetting)
    {
        $this->_quoteSetting = $_quoteSetting;

        return $this;
    }
}