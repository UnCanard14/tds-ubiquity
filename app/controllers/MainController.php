<?php
namespace controllers;

 use classes\LocalBasket;
 use models\Basket;
 use models\Basketdetail;
 use models\Order;
 use models\Product;
 use models\Section;
 use models\Timeslot;
 use models\User;
 use services\dao\OrgaRepository;
 use services\ui\UiStoreService;
 use Ubiquity\attributes\items\di\Autowired;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\controllers\auth\AuthController;
 use Ubiquity\controllers\auth\WithAuthTrait;
 use Ubiquity\controllers\Router;
 use Ubiquity\orm\DAO;
 use Ubiquity\utils\http\URequest;
 use Ubiquity\utils\http\UResponse;
 use Ubiquity\utils\http\USession;

 /**
 * Controller MainController
 **/

class MainController extends ControllerBase{
    use WithAuthTrait;

    public function initialize()
    {
        parent::initialize();
        $this->ui = new UiStoreService($this);
    }


    #[Autowired]
    private OrgaRepository $repo;

    /**
     * @return OrgaRepository
     */
    public function getRepo(): OrgaRepository
    {
        return $this->repo;
    }

    /**
     * @param OrgaRepository $repo
     */
    public function setRepo(OrgaRepository $repo): void
    {
        $this->repo = $repo;
    }

    #[Route ('_default', name:'home')]
	public function index(){
        $numOrders = count(DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]));
        $articlesPromo = DAO::getAll(Product::class, 'promotion< ?', false, [0]);
        $numBaskets = count(DAO::getAll(Basket::class, 'idUser= ?', false, [USession::get("idUser")]));
        //$this->jquery->get(Router::url("section", [1]), ".detail");
        $recentlyViewedProducts = USession::get('recentlyViewedProducts');
		$this->jquery->renderDefaultView(['numOrders'=>$numOrders ,'articlesPromo'=>$articlesPromo, 'numBaskets'=>$numBaskets, 'recentlyViewedProducts'=>$recentlyViewedProducts]);
	}

    protected function getAuthController(): AuthController
    {
        // TODO: Implement getAuthController() method.
        return $this->_auth??= new \controllers\AuthController($this);
    }
    //************************************* Patite store *********************************
    //Affiche le store avec ses sections
    #[Route ('store', name:'store')]
    public function storePage(){
       $store = DAO::getAll(Product::class, false, false);
       $sections = DAO::getAll(Section::class, false, false);
       $this->loadDefaultView(['store'=>$store, 'sections'=>$sections]);
    }

    //Affiche la liste des commandes
    #[Route ('order', name:'order')]
    public function orderPage(){
        $orders = DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['orders'=>$orders]);
    }

    //Affiche le détail d'un produit
    #[Route(path: "productSheet/{idSection}/{idProduct}",name: "productSheet")]
    public function productSheet($idSection,$idProduct){
        $article = DAO::getById(Product::class, $idProduct, false);
        $section = DAO::getById(Section::class, $idSection, false);
        $assoProducts = $article->getAssociatedproducts();
        $rvp = USession::get("recentlyViewedProducts");
        $rvp[] = $article;
        USession::set("recentlyViewedProducts", $rvp);
        $this->loadDefaultView(['article'=>$article, 'section'=>$section, 'assoProducts'=>$assoProducts]);
    }

    //Affiche les produits d'une section
    #[Route(path: "section/{id}",name: "section")]
    public function section($id){
        $articles = DAO::getAll(Product::class, 'idSection= ?', false, [$id]);
        $section = DAO::getById(Section::class, $id, false);
        $this->loadDefaultView(['articles'=>$articles, 'section'=>$section]);
    }

    //******************************************** Partie panier *****************************************
    //Permet de créer un nouveau panier
    #[Route ('newBasket', name:'newBasket')]
    public function newBasket(){
        $reponse = URequest::post("name");
        if($reponse != null && $reponse != "_default"){
            $currentUser = DAO::getById(User::class, USession::get("idUser"), false);
            $newBaset = new Basket();
            $newBaset->setUser($currentUser);
            $newBaset->setName($reponse);
            UResponse::header('location', '/'.Router::path('basket'));
        }
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', ['basketdetails.product'], [USession::get("idUser")]);
        $this->loadDefaultView(['baskets'=>$baskets]);
    }

    //Affiche la liste des paniers de l'utilisateur
    #[Route ('Basket', name:'basket')]
    public function basket(){
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['baskets'=>$baskets]);
    }

    //Affiche le pannier en cours aves ses détails
    #[Route(path: "currentBasket",name: "main.currentBasket")]
    public function currentBasket(){
        $localBasket = USession::get('defaultBasket');
        $basketDetails = $localBasket->getProducts();
        $quantity = $localBasket->getQuantity();
        $totalDiscount = $localBasket->getTotalDiscount();
        $fullPrice = $localBasket->getTotalFullPrice();
        $this->jquery->postFormOn('change','input',Router::path('updateBasketQuantity'),'frmBasket','#toUpdate');
        $this->jquery->renderDefaultView(['basketDetails'=>$basketDetails, 'fullePrice'=> $fullPrice, 'totalDiscount'=>$totalDiscount, 'quantity'=>$quantity]);
    }

    #[Route(path: "updateBasketQuantity",name: "updateBasketQuantity")]
    public function updateBasketQuantity(){
        $localBasket = USession::get('defaultBasket');
        for ($i = 0; $i < count($_POST["id"]); $i++) {
            $localBasket->updateQuantity(DAO::getOne(Product::class,$_POST["id"][$i],false), $_POST["quantity"][$i]);
        }
        $quantity = $localBasket->getQuantity();
        $totalDiscount = $localBasket->getTotalDiscount();
        $fullPrice = $localBasket->getTotalFullPrice();
        $this->loadDefaultView(['fullePrice'=> $fullPrice, 'totalDiscount'=>$totalDiscount, 'quantity'=>$quantity]);
    }

    //Ajoute un article au panier par default
	#[Route(path: "addArticleToDefaultBasket/{idProduct}",name: "addArticleToDefaultBasket")]
	public function addArticleToDefaultBasket($idProduct){
        $article = DAO::getById(Product::class, $idProduct, false);
        $localBasket = USession::get('defaultBasket');
        $localBasket->addProduct($article, 1);
        UResponse::header('location', '/'.Router::path('store'));
	}

    #[Route(path: "deleteProductFromBasket/{id}",name: "deleteProductFromBasket")]
    public function deleteProductFromBasket($id){
        $localBasket = USession::get('defaultBasket');
        $localBasket->deleteAnArticle($id);
        UResponse::header('location', '/'.Router::path('main.currentBasket'));
    }

    //Ajoute un article à un panier selectionné
	#[Route(path: "addArticleToSpecificBasket/{idBasket}/{idProduct}",name: "addArticleToSpecificBasket")]
	public function addArticleToSpecificBasket($idBasket, $idProduct){
        $basket = DAO::getById(Basket::class, $idBasket, false);
        $article = DAO::getById(Product::class, $idProduct, false);
        $basketDetail = new Basketdetail();
        $basketDetail->setProduct($article);
        $basketDetail->setBasket($basket);
        $basketDetail->setQuantity(1);
        UResponse::header('location', '/'.Router::path('store'));
    }

    #[Route(path: "clearBasket",name: "clearBasket")]
    public function clearBasket(){
        $localBasket = USession::get('defaultBasket');
        $localBasket->clearBasket();
        UResponse::header('location', '/'.Router::path('basket'));
    }

    #[Route(path: "selectBasket/{idBasket}",name: "selectBasket")]
    public function selectBasket($idBasket){
        $basket = DAO::getById(Basket::class, $idBasket, false);
        $basket = new LocalBasket($basket);
        USession::set('defaultBasket', $basket);
        UResponse::header('location', '/'.Router::path('store'));
    }

    //****************************************** Partie commande *****************************************

	#[Route(path: "orderCollection",name: "orderingCollection")]
	public function orderingCollection(){
        $slots = DAO::getAll(Timeslot::class, 'full= ?', false, [0]);
        echo '<pre>';
      //  print_r($slots);
        echo '</pre>';



        foreach ($slots as $slot){
          //  echo $slot->getSlotDate() . "<br>";
        }
        //$this->jquery->sc
        $this->loadDefaultView(['slots'=>$slots]);
	}


	#[Route(path: "summary",name: "summary")]
	public function summary(){
		if(URequest::post("slot") != null){
		    $slot = URequest::post("slot");
        }
        $slot = DAO::getById(Timeslot::class, $slot, false);
		$this->loadDefaultView(['slots'=>$slot]);

	}



}
