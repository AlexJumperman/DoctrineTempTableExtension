<?php

namespace AlexJumperman\TempTableBundle\Utils;


use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

class NativeQuery extends AbstractQuery
{
    private $tempTable;

    public function __construct(EntityManagerInterface $em, TempTable $tempTable)
    {
        $this->tempTable = $tempTable;
        parent::__construct($em);
    }

    /**
     * @var string
     */
    private $_sql;

    /**
     * Sets the SQL of the query.
     *
     * @param string $sql
     *
     * @return NativeQuery This query instance.
     */
    public function setSQL($sql)
    {
        $this->_sql = $sql;

        return $this;
    }

    /**
     * Gets the SQL query.
     *
     * @return mixed The built SQL query or an array of all SQL queries.
     *
     * @override
     */
    public function getSQL()
    {
        return $this->_sql;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doExecute()
    {
        $parameters = array();
        $types      = array();

        foreach ($this->getParameters() as $parameter) {
            $name  = $parameter->getName();
            $value = $this->processParameterValue($parameter->getValue());
            $type  = ($parameter->getValue() === $value)
                ? $parameter->getType()
                : Query\ParameterTypeInferer::inferType($value);

            $parameters[$name] = $value;
            $types[$name]      = $type;
        }

        if ($parameters && is_int(key($parameters))) {
            ksort($parameters);
            ksort($types);

            $parameters = array_values($parameters);
            $types      = array_values($types);
        }

        $this->tempTable->create();

        return $this->_em->getConnection()->executeQuery(
            $this->_sql, $parameters, $types, $this->_queryCacheProfile
        );
    }
}