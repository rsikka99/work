<?php

namespace MPSToolbox\Entities;

trait EntityTrait {
    /**
     * @param $id
     * @return null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Zend_Exception
     */
    public static function find($id) {
        /** @var $e \Doctrine\ORM\EntityManager */
        $e = \Zend_Registry::get('Doctrine\ORM\EntityManager');
        return $e->find(get_called_class(), $id);
    }

    public static function getRepository() {
        /** @var $e \Doctrine\ORM\EntityManager */
        $e = \Zend_Registry::get('Doctrine\ORM\EntityManager');
        return $e->getRepository(get_called_class());
    }

    public function save() {
        /** @var $e \Doctrine\ORM\EntityManager */
        $e = \Zend_Registry::get('Doctrine\ORM\EntityManager');
        $e->persist($this);
        $e->flush();
    }

    public function delete() {
        /** @var $e \Doctrine\ORM\EntityManager */
        $e = \Zend_Registry::get('Doctrine\ORM\EntityManager');
        $e->remove($this);
        $e->flush();
    }

    public function toArray() {
        return get_object_vars($this);
    }
}
