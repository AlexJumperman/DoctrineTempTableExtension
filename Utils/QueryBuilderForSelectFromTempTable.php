<?php

namespace AlexJumperman\TempTableBundle\Utils;


use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;

class QueryBuilderForSelectFromTempTable extends QueryBuilder
{
    private $tempTableRepository;
    private $alias;

    public function __construct(TempTableRepository $tempTableRepository, $alias)
    {
        $this->tempTableRepository = $tempTableRepository;
        $this->alias = $alias;
        $this->select('*')->from($this->tempTableRepository->getTempTable()->getTableName(), $this->alias);
        parent::__construct($this->tempTableRepository->getEntityManager()->getConnection());
    }

    public function getSQL()
    {
        $this->tempTableRepository->getTempTable()->create();
        return parent::getSQL();
    }

    public function getQuery()
    {
        return $this->tempTableRepository->getEntityManager()->createNativeQuery($this->getSQL(), $this->getRsm());
    }

    protected function getRsm()
    {
        $className = $this->tempTableRepository->getOriginRepository()->getClassName();
        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($this->tempTableRepository->getEntityManager());
        $classMetadata = $cmf->getMetadataFor($className);
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($className, 'u');
        foreach( $classMetadata->fieldMappings as $id => $obj ){
            $rsm->addFieldResult('u', $obj["columnName"], $obj["fieldName"]);
        }
        return $rsm;
    }
}