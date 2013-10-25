<?php

/**
 * Class My_Feature_DbTableInterface
 */
interface My_Feature_DbTableInterface
{
    /**
     * Should get a array of feature names and return them in string form
     *
     * @return string[]
     */
    public function getFeatures ();
}