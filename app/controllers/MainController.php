<?php
namespace controllers;

 use classes\LocalBasket;
 use models\Basket;
 use models\Basketdetail;
 use models\Order;
 use models\Product;
 use models\Section;
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

    #[Route ('store', name:'store')]
    public function storePage(){
       $store = DAO::getAll(Product::class, false, false);
       $sections = DAO::getAll(Section::class, false, false);
       $this->loadDefaultView(['store'=>$store, 'sections'=>$sections]);
    }

    #[Route ('order', name:'order')]
    public function orderPage(){
        $orders = DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['orders'=>$orders]);
    }

    #[Route ('newBasket', name:'newBasket')]
    public function newBasket(){
        if(URequest::post("name") != null){
            echo URequest::post("name");
            $currentUser = DAO::getById(User::class, USession::get("idUser"), false);
            $newBaset = new LocalBasket(URequest::post("name"), $currentUser);
            $newBaset->saveInDatabase();
            UResponse::header('location', '/'.Router::path('basket'));
        }
        $this->loadDefaultView();
//        echo '<pre>';
//        print_r($orders);
//        echo '</pre>';
    }

    #[Route ('Basket', name:'basket')]
    public function basket(){
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['baskets'=>$baskets]);
    }

	#[Route(path: "section/{id}",name: "section")]
	public function section($id){
        $articles = DAO::getAll(Product::class, 'idSection= ?', false, [$id]);
        $section = DAO::getById(Section::class, $id, false);
		$this->loadDefaultView(['articles'=>$articles, 'section'=>$section]);
	}

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


	#[Route(path: "addArticleToDefaultBasket/{idProduct}",name: "addArticleToDefaultBasket")]
	public function addArticleToDefaultBasket($idProduct){
        $article = DAO::getById(Product::class, $idProduct, false);
        $localBasket = USession::get('defaultBasket');
        $localBasket->addProduct($article, 1);
        UResponse::header('location', '/'.Router::path('store'));
	}

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


	#[Route(path: "currentBasket",name: "main.currentBasket")]
	public function currentBasket(){
        $localBasket = USession::get('defaultBasket');
        $products = $localBasket->getProducts();
        $quantity = $localBasket->getQuantity();
        $totalDiscount = $localBasket->getTotalDiscount();
        $fullPrice = $localBasket->getTotalFullPrice();
        $this->loadDefaultView(['products'=>$products, 'fullePrice'=> $fullPrice, 'totalDiscount'=>$totalDiscount, 'quantity'=>$quantity]);
	}

}
