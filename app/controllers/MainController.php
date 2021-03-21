<?php
namespace controllers;

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
        USession::set('recentlyViewedProducts',[]);
        $numOrders = count(DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]));
        $articlesPromo = DAO::getAll(Product::class, 'promotion< ?', false, [0]);
        $numBaskets = count(DAO::getAll(Basket::class, 'idUser= ?', false, [USession::get("idUser")]));
        //$this->jquery->get(Router::url("section", [1]), ".detail");
        $recentlyViewedProducts = USession::get('recentlyViewedProducts');
                echo '<pre>';
        print_r($recentlyViewedProducts);
        echo '</pre>';
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
        array_push($rvp, $article);
        USession::set("recentlyViewedProducts", $rvp);
        $this->loadDefaultView(['article'=>$article, 'section'=>$section, 'assoProducts'=>$assoProducts]);
	}


	#[Route(path: "addArticleToDefaultBasket/{idProduct}",name: "addArticleToDefaultBasket")]
	public function addArticleToDefaultBasket($idProduct){
        $basket = DAO::getOne(Basket::class, 'idUser= ?', false, [USession::get("idUser")]);
        $Basketdetails = new Basketdetail();
        $Basketdetails->setBasket($basket);
        $Basketdetails->setIdProduct($idProduct);
        $Basketdetails->setQuantity(1);
        if(DAO::save($Basketdetails)){
            UResponse::header('location', '/');
        }else{
            echo $Basketdetails.'not added in database';
        }
	}

	#[Route(path: "addArticleToSpecificBasket/{idBasket}/{idProduct}",name: "addArticleToSpecificBasket")]
	public function addArticleToSpecificBasket($idBasket, $idProduct){
		
	}

}
