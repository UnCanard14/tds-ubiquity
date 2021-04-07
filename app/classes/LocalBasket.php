<?php


namespace classes;

use Ajax\semantic\widgets\datatable\DataTable;
use models\Product;
use ArrayObject;
use models\Basket;
use models\Basketdetail;
use models\User;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\UResponse;
use Ubiquity\utils\http\USession;

class LocalBasket
{
    private $name;
    private $basket;
    private $user;
    private $products;
    private $total;

    public function __construct($id, $basket)
    {
        $this->idBasket = $basket->getId();
        $this->basket = $basket;
//        $this->basket = DAO::getById(Basketdetail::class,$id,false);
//        $this->basket = new Basket();
//        $this->basket->setName($this->name);
//        $this->basket->setUser($this->user);
//
//        $this->name = $name;
//        $this->user = $user;
//        $this->total = 0;
    }


    public function addProduct($article, $quantity)
    {
        if(DAO::getOne(Basketdetail::class,'idProduct = ?',false,[$article->getId()])){
            $this->jslog("There already a product");
        }else{
            $this->jslog("Add".$article->getName(). "product in ". $quantity);

            $basketDetail = new Basketdetail();
            $basketDetail->setBasket($this->basket);
            echo '<pre>';
            print_r($basketDetail);
            echo '</pre>';
            $basketDetail->setProduct($article);
            $basketDetail->setQuantity($quantity);
            DAO::save($basketDetail);
        }
    }

    public function getProducts()
    {
        $baskets = DAO::getAll(Basket::class, 'id= ?', ['basketdetails.product'], [$this->idBasket]);
        return $baskets;
    }

    //ok
    public function clearBasket()
    {
        if($res=DAO::deleteAll(Basketdetail::class, 'id = ?',[$this->idBasket])){
            return $res;
        }
        return -1;
    }

    //ok
    public function updateQuantity($article, $quantity)
    {
        $basketdetail=DAO::getOne(Basketdetail::class,'idProduct = ?',false,[$article->getId()]);
        $basketdetail->setQuantity($quantity);
        if(DAO::save($basketdetail)){
            return 1;
        }
        return -1;
    }

    public function getTotalFullPrice()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        $basketDetails = $baskets->getBasketdetails();
        $somme =0;
        foreach ($basketDetails as $basketDetail){
            $somme += $basketDetail->getProduct()->getPrice() * $basketDetail->getQuantity();
        }
        return $somme;
    }

    public function getTotalDiscount()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        $basketDetails = $baskets->getBasketdetails();
        $somme =0;
        foreach ($basketDetails as $basketDetail){
            $somme += $basketDetail->getProduct()->getPromotion() * $basketDetail->getQuantity();
        }
        return $somme;
    }

    public function getQuantity()
    {
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        $basketDetails = $baskets->getBasketdetails();
        $somme =0;
        foreach ($basketDetails as $basketDetail){
            $somme += $basketDetail->getQuantity();
        }
        return $somme;
    }

    private function jslog($messageLog){
        echo "<script> console.log('$messageLog ')</script>";
    }

}