<?php

namespace App\Modules\Product\Rule\ListAll;

use App\Setup\Rule;

class ListAllProducts extends Rule
{
    public function isApplicable($args)
    {
        return true;
    }

    public function apply($args)
    {

        return $this->getRepository()->getAllProducts();
    }
}