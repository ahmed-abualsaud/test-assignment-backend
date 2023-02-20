<?php

require __DIR__ . '/vendor/autoload.php';


use App\Setup\App;

App::run();

// $where = ["sku" => "111-aaa", "name" => "ahmed", "price" => 123];
// echo print_r(str_replace("=", "='", http_build_query($where,'','\' AND ')));

// function foo($where, ...$select) {

//     return $selectColumns = empty($select)? "*": implode(", ", $select);
// }

// echo print_r(foo(["123" => "HMED"]));
