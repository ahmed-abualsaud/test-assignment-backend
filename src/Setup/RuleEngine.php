<?php

namespace App\Setup;

class RuleEngine
{
    private $rules;

    public function __construct($rules)
    {
        $this->rules = $rules;
    }
    public function applyAll($args)
    {
        $results = [];
        foreach ($this->rules as $rule) {

            if ($rule->isApplicable($args)) {
                $result = $rule->apply($args);

                if (! empty($result)) {
                    if (! is_array($result)) {
                        $results[] = $result;
                    } else {
                        $results = array_merge($results, $result);
                    }
                }
            }
        }

        if (count($results) == 1) {
            return $results[0];
        }

        return $results;
    }

    public static function run($rules, $args = null)
    {
        return (new RuleEngine($rules))->applyAll($args);
    }
}