<?php

/**
 * Class My_Feature_AdapterInterface
 */
interface My_Feature_AdapterInterface
{
    /**
     * Should get a array of feature names and return them in string form
     *
     * @return string[]
     */
    public function getFeatures ();
}