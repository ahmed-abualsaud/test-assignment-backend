<?php

namespace App\Setup;

abstract class Rule
{
    private $repositories;

    public function __construct($repositories)
    {
        $this->repositories = $repositories;
    }

    public function getRepositories()
    {
        return $this->repositories;
    }

    abstract public function isApplicable($args);
    abstract public function apply($args);
}