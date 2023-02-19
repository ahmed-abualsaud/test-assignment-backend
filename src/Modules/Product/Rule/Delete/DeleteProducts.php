<?php

namespace App\Modules\Product\Rule\Delete;

use App\Setup\Rule;

class DeleteProducts extends Rule
{
    public function isApplicable($args)
    {
        if (! empty($args)) {
            return true;
        }

        return false;
    }

    public function apply($ids)
    {
        return $this->getRepository()->deleteProducts($ids);
    }
}