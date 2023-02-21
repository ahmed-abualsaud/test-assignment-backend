<?php

require __DIR__ . '/vendor/autoload.php';

use App\Setup\App;

App::run();

// $where = [
//     "sku" => "111-aaa", 
//     "name" => "ahmed", 
//     "price" => 123,
//     "type" => DBQuery::whereIn(["DVD-disc", "Book", "Furniture"])
// ];

// function andWhere($criteria)
// {
//     $anded = "";

//     foreach ($criteria as $key => $value) {
//         if (gettype($value) === "string" && Helper::string_starts_with($value, "IN(")) {

//             $anded .= $key." ".$value." AND ";
//         } else {
//             $anded .= $key."='".$value."' AND ";
//         }
//     }
//     return substr($anded, 0, strlen($anded) - 5);
// }

// echo (andWhere($where));

// function foo($where, ...$select) {

//     return $selectColumns = empty($select)? "*": implode(", ", $select);
// }

// echo print_r(foo(["123" => "HMED"]));
