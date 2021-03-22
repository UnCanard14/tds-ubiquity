<?php


namespace classes;

use models\Product;
use ArrayObject;
use models\Basket;
use models\Basketdetail;
use models\User;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\UResponse;

class LocalBasket
{
    private $name;
    private $products;
    private $user;
    private $total;

    public function __construct($name, $user){
        $this->name = $name;
        $this->user = $user;
        $this->total = 0;
        $this->products = array();
    }

    public function addProduct($article, $quantity){
        if(!isset ($this->products[$article->getId()])){
            $this->products[$article->getId()]['quantity'] = $quantity;
            $this->products[$article->getId()]['product'] = $article;
        }else{
            $this->products[$article->getId()]['quantity'] += $quantity;
        }
    }

    public function updateQuantity($article, $quantity){
        $this->products[$article->getId()]['quantity'] = $quantity;
    }

    public function getTotalFullPrice(){
        foreach ($this->products as $key => $value){
            $this->total += $value['product']->getPrice();
        }
        return $this->total;
    }

    public function getTotalDiscount(){
        $priceDscount = 0;
        foreach ($this->products as $key => $value){
            $priceDscount += $value['product']->getPrice() - $value['product']->getPromotion();
        }
        return $priceDscount;
    }

    public function getQuantity(){
        $count = 0;
        foreach ($this->products as $key => $value){
            $count += $value['quantity'];
        }
        return $count;
    }


    public function saveInDatabase(){
        $basket = new Basket();
        $basket->setUser($this->user);
        $basket->setName($this->name);

        foreach ($this->products as $key => $value){
            $Basketdetails = new Basketdetail();
            $Basketdetails->setBasket($basket);
            $Basketdetails->setIdProduct($key);
            $Basketdetails->setQuantity($value['quantity']);
        }
//        if(!DAO::save($Basketdetails)){
//            return false;
//        }
        return true;
    }

}