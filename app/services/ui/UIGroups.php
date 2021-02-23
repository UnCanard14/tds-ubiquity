<?php
namespace services\ui;

 use Ubiquity\controllers\Controller;
 use Ubiquity\utils\http\URequest;

 /**
  * Class UIGroups
  */
class UIGroups extends \Ajax\php\ubiquity\UIService{
    public function __construct(Controller $controller)
    {
        parent::__construct($controller);
        //$this->jquery->
        if(!URequest::isAjax()) {
            $this->jquery->getHref('a[data-target]', '', ['hasLoader' => 'internal', 'historize' => false,'listenerOn'=>'body']);
        }
    }

    public function listGroups(array $groups){
        $dt =$this->semantic->dataTable('dt-groups', Groups::class, $groups);
        //dt->setField(['name', 'email']);
        //$td->

    }


}
