<?php

namespace AlexJumperman\TempTableBundle\Utils;


use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class QueryBuilderForSelectIntoTempTable extends QueryBuilder
{
    private $em;
    private $repository;
    private $tableName;
    private $alias;

    public function __construct(EntityManagerInterface $entityManager, EntityRepository $repository, $alias)
    {
        $this->em = $entityManager;
        $this->repository = $repository;
        $this->tableName = $this->em->getClassMetadata($this->repository->getClassName())->getTableName();
        $this->alias = $alias;
        $this->select('*')->from($this->tableName, $this->alias);
        parent::__construct($entityManager->getConnection());
    }

    public function createTempTableRepository($tempTableName)
    {
        return new TempTableRepository($this->em, $this->repository, $this->getTempTable($tempTableName));
    }

    private function getTempTable($tempTableName)
    {
        return new TempTable($this->em->getConnection(), $tempTableName, $this->getSQL());
    }
}