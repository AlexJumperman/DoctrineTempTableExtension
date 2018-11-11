# DoctrineTempTableExtension
## Problem
let's imagine that we have online-store with 1M products. On one specific category page we need to work only with 100 products from the whole stack, and we need to get:
1. total products count on this page
2. first 10 product entities sorting by some order
3. products count by every single filter etc.

Queries by the entire stack will not be effective. More efficient way - select needed products into temporary table and executing this queries from temporary table.

## Install
composer require alexjumperman/doctrinetemptable

## Usage
### 1. Using repository trait

``` php
<?php

namespace AppBundle\Repository;

use AlexJumperman\TempTableBundle\Utils\TempTableTrait;

class ProductRepository extends \Doctrine\ORM\EntityRepository
{
    use TempTableTrait;
}
```
#### After trait was using we continue in controller
``` php
<?php

namespace AppBundle\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $qb = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Product')
            ->createQueryBuilderForTempTable('p');
    }
}
```
### 2. Or we can use service factory
``` php
<?php

namespace AppBundle\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $repository = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Product');
        $qb = $this->get('alex_jumperman_temp_table.factory')
            ->createQueryBuilderForTempTable($repository, 'p');
    }
}
```
## Workflow
When we have query builder instance for temporary table, we need to construct it for our requirements. In our case we need to select all products which relating to specific category.
``` php
$qb->where('p.category_id = 1');
```
After the query builder is configured, we can create a repository of our temporary table. In fact, it will be a certain analogue of the doctrine repository that works with the temporary table storage.
``` php
$tempRepository = $qb->createTempTableRepository('temp_products_table');
```
When the temporary repository is created, we can configure queries to select the necessary data.
``` php
$result1 = $tempRepository
            ->getEntityManager()
            ->getConnection()
            ->fetchColumn($tempRepository->createQueryBuilder('p')->select('count(p)')->getSQL());
$result2 = $tempRepository
            ->createQueryBuilder('p')
            ->setMaxResults(10)
            ->orderBy('p.price')
            ->getQuery()
            ->getResult();
```
## Whole process example
``` php
<?php

namespace AppBundle\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $repository = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Product');
        $tempRepository = $this->get('alex_jumperman_temp_table.factory')
            ->createQueryBuilderForTempTable($repository, 'p')
            ->where('p.category_id = 1')
            ->createTempTableRepository('temp_products_table');
        $result1 = $tempRepository
            ->getEntityManager()
            ->getConnection()
            ->fetchColumn($tempRepository->createQueryBuilder('p')->select('count(p)')->getSQL());
        $result2 = $tempRepository
            ->createQueryBuilder('p')
            ->setMaxResults(10)
            ->orderBy('p.price')
            ->getQuery()
            ->getResult();
    }
}
```
