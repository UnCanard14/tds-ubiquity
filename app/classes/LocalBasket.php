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
    private $basket;

    public function __construct($basket)
    {
        $this->idBasket = $basket->getId();
        $this->basket = $basket;
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
        $baskets = DAO::getById(Basket::class, $this->idBasket, ['basketdetails.product']);
        return $baskets->getBasketdetails();
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