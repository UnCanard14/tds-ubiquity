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

    public function getProducts(){
        return $this->products;
    }

    public function updateQuantity($article, $quantity){
        $this->products[$article->getId()]['quantity'] = $quantity;
    }

    public function getTotalFullPrice(){
        foreach ($this->products as $key => $value){
            $this->total += $value['product']->getPrice() * $value['quantity'];
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
        try {
            DAO::beginTransaction();
            $basket = new Basket();
            $basket->setName($this->name);
            $basket->setUser($this->user);
            if (DAO::save($basket)) {
                foreach ($this->products as $value) {
                    $basketDetail = new Basketdetail();
                    $basketDetail->setBasket($basket);
                    if(isset($value['product']) && isset($value['quantity'])){
                        $basketDetail->setProduct($value['product']);
                        $basketDetail->setQuantity($value['quantity']);
                    }
                    DAO::save($basketDetail);
                }
            }
            return DAO::commit();
        }
        catch(\Exception $e){
            DAO::rollBack();
            return false;
        }
    }

}