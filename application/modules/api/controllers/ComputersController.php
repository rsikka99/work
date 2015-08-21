<?php

use MPSToolbox\Entities\ExtComputerEntity;
use Tangent\Controller\Action;

/**
 * Class Api_ComputersController
 *
 * This controller handles everything to do with creating/updating computers
 */
class Api_ComputersController extends Action implements \Tangent\Grid\DataAdapter\DataAdapterInterface
{

    /** @var \Doctrine\ORM\QueryBuilder */
    private $qb;

    public function init()
    {
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;

        $this->qb = ExtComputerEntity::getRepository()
            ->createQueryBuilder('c')
            ->select('c, m, d')
            ->join('c.manufacturer','m')
            ->leftJoin('c.dealerHardware','d','WITH','d.dealer=:dealerId')
            ->setParameter('dealerId', $dealerId)
            //->where('d.dealerId='.intval($dealerId))
            ->orderBy('m.displayname')
            ->orderBy('c.modelName');
    }


    /**
     *
     */
    public function indexAction ()
    {
        $hardwareId = $this->getParam('hardwareId', false);

        if ($hardwareId !== false)
        {
            $this->sendJson(ExtComputerEntity::find($hardwareId)->toArray());
            return;
        }

        $searchTerm = $this->getParam('q', false);
        $pageLimit  = $this->getParam('page_limit', 50);
        $page       = $this->getParam('page', 1);

        $this->qb->setMaxResults($pageLimit);

        if (strlen(trim($searchTerm)) > 0) {
            $this->qb->where('c.modelName like :q')->setParameter('q', '%'.trim($searchTerm).'%');
        }

        if ($page > 1) {
            $this->qb->setFirstResult($pageLimit * ($page - 1));
        }

        $this->sendJson([
            'result' => $this->qb->getQuery()->getArrayResult(),
            'total' => $this->qb->select('count(c)')->getQuery()->getSingleScalarResult()
        ]);
    }

    public function onlineAction() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->query('update ext_dealer_hardware set `online`=? where dealerId=? and id=?', [
            $this->getParam('online')=='true'?'1':'0',
            \Zend_Auth::getInstance()->getIdentity()->dealerId,
            intval($this->getParam('id'))
        ])->execute();
        $this->sendJson(array('ok'));
    }

    public function gridListAction ()
    {
        $postData          = $this->getAllParams();
        $filterSearchIndex = $this->_getParam('filterSearchIndex', null);
        $filterSearchValue = $this->_getParam('filterSearchValue', null);


        $columnFactory = new \Tangent\Grid\Order\ColumnFactory([
            'category', 'modelName', 'oemSku', 'dealerSku', 'online', 'isSystemDevice'
        ]);

        $gridRequest  = new \Tangent\Grid\Request\JqGridRequest($postData, $columnFactory, 1, 50);
        $gridResponse = new \Tangent\Grid\Response\JqGridResponse($gridRequest);

        $filterCriteriaValidator = new Zend_Validate_InArray(['haystack' => ['category', 'modelName', 'oemSku', 'dealerSku', 'online']]);

        if ($filterSearchIndex !== null && $filterSearchValue !== null && $filterCriteriaValidator->isValid($filterSearchIndex))
        {
            $this->addFilter(new \Tangent\Grid\Filter\Contains($filterSearchIndex, $filterSearchValue));
        }

        $gridService = new \Tangent\Grid\Grid($gridRequest, $gridResponse, $this);
        $this->sendJson($gridService->getGridResponseAsArray());
    }

    /*
    =====================================
     DataAdapterInterface implementation
    =====================================
    */
    public function addFilter(\Tangent\Grid\Filter\AbstractFilter $filter)
    {
        $this->qb->where('c.modelName like :q')->setParameter('q', '%'.trim($filter->getFilterValue()).'%');
    }

    public function addOrderBy(\Tangent\Grid\Order\Column $orderBy)
    {
    }

    public function fetchAll()
    {
        $result = [];
        foreach ($this->qb->getQuery()->getArrayResult() as $line) {
            if (!isset($line['dealerHardware'][0])) $line['dealerHardware'][0] = ['dealerSku'=>'', 'oemSku'=>''];
            $row=[
                'id'=>$line['id'],
                'name'=>$line['manufacturer']['displayname'].' '.$line['modelName'],
                'dealerSku'=>$line['dealerHardware'][0]['dealerSku'],
                'oemSku'=>$line['dealerHardware'][0]['oemSku'],
                'category'=>$line['category'],
                'online'=>$line['dealerHardware'][0]['online'],
            ];

            if ($row['online']) {
                $row['online'] = '<input type="checkbox" checked="checked" onclick="online_click(this, '.$line['id'].')">';
            } else {
                $row['online'] = '<input type="checkbox" onclick="online_click(this, '.$line['id'].')">';
            }

            $result[] = $row;
        }
        return $result;
    }

    public function count()
    {
        $qb = clone $this->qb;
        return $qb->select('count(c)')->getQuery()->getSingleScalarResult();
    }

    public function countWithoutFilter()
    {
        return $this->count();
    }

    public function setLimit($limit)
    {
        $this->qb->setMaxResults($limit);
    }

    public function setStartRecord($startRecord)
    {
        $this->qb->setFirstResult($startRecord);
    }


}