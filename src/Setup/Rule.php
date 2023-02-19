<?php

namespace App\Setup;

abstract class Rule
{
    private $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    abstract public function isApplicable($args);
    abstract public function apply($args);
}