<?php

namespace AlexJumperman\TempTableBundle\Utils;


trait TempTableTrait
{
    public function createQueryBuilderForTempTable($alias)
    {
        return new QueryBuilderForSelectIntoTempTable($this->_em, $this, $alias);
    }
}