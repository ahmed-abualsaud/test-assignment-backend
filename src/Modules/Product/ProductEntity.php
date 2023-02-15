<?php

namespace App\Modules\Product;

use App\Database\Entity;

class ProductEntity extends Entity
{
    #@Column()
    public $id;

    #@Column()
    public $title;
  
    public $body;
  
    public $author_id;
  
    public $date;
  
    public $views;
  
    public $finished;
}