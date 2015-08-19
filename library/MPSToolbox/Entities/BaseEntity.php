<?php

namespace MPSToolbox\Entities;

class BaseEntity {
    /**
     * @param $id
     * @return null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Zend_Exception
     */

    /**
     * @return \Doctrine\ORM\EntityManager
     * @throws \Zend_Exception
     */
    public static function em() {
        return \Zend_Registry::get('Doctrine\ORM\EntityManager');
    }

    public static function find($id) {
        return self::em()->find(get_called_class(), $id);
    }

    public static function getRepository() {
        return self::em()->getRepository(get_called_class());
    }

    public function save() {
        self::em()->persist($this);
        self::em()->flush();
    }

    public function delete() {
        self::em()->remove($this);
        self::em()->flush();
    }

    public function toArray() {
        $result = [];
        $cols = self::em()->getClassMetadata(get_class($this))->getColumnNames();
        foreach($cols as $col) {
            $v = null;
            if (method_exists($this,$getter='get'.ucfirst($col))) $v=$this->$getter();
            if (method_exists($this,$getter='is'.ucfirst($col))) $v=$this->$getter();
            if ($v instanceof \DateTime) {
                /** @var $v \DateTime */
                $v = str_replace(' 00:00:00','',date('Y-m-d H:i:s', $v->getTimestamp()));
            }
            $result[$col] = $v;
        }
        return $result;
    }

    public function populate($data) {
        $cols = self::em()->getClassMetadata(get_class($this))->getColumnNames();
        foreach($cols as $key) {
            if (!isset($data[$key])) continue;
            $method = 'set'.ucfirst($key);
            $this->$method($data[$key]);
        }
    }

    public function referenceById($propertyName, $entityName, $id) {
        $method = 'set'.ucfirst($propertyName);
        $this->$method(self::em()->getReference($entityName, $id));
    }
}
