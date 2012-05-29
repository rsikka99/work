<?php

class GenericObject extends stdClass
{

    public function __construct (array $options = null)
    {
        if (is_array($options))
        {
            $this->setOptions($options);
        }
    }

    public function setOptions (array $options)
    {
        foreach ( $options as $key => $value )
        {
            $this->$key = $value;
        }
        return $this;
    }

}
?>
