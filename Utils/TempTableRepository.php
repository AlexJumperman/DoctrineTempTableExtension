<?php

namespace AlexJumperman\TempTableBundle\Utils;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TempTableRepository
{
    private $em;
    private $repository;
    private $tempTable;

    public function __construct(EntityManagerInterface $entityManager, EntityRepository $repository, TempTable $tempTable)
    {
        $this->em = $entityManager;
        $this->repository = $repository;
        $this->tempTable = $tempTable;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function getOriginRepository()
    {
        return $this->repository;
    }

    public function getTempTable()
    {
        return $this->tempTable;
    }

    public function createQueryBuilder($alias)
    {
        return new QueryBuilderForSelectFromTempTable($this, $alias);
    }
}