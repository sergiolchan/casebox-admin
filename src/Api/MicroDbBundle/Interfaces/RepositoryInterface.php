<?php

namespace Api\MicroDbBundle\Interfaces;

interface RepositoryInterface
{
    /**
     * @return ObjectInterface 
     */
    public function getObjectClass();

    /**
     * @return string
     */
    public function getDbAlias();
}
