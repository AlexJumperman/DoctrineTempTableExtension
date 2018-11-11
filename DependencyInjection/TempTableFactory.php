<?php

namespace AlexJumperman\TempTableBundle\DependencyInjection;


use AlexJumperman\TempTableBundle\Utils\QueryBuilderForSelectIntoTempTable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TempTableFactory
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createQueryBuilderForTempTable(EntityRepository $repository, $alias)
    {
        return new QueryBuilderForSelectIntoTempTable($this->em, $repository, $alias);
    }
}