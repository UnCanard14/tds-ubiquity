<?php
namespace services\ui;

 use Ubiquity\controllers\Controller;

 /**
  * Class UIGroups
  */
class UIGroups extends \Ajax\php\ubiquity\UIService{
    public function __construct(Controller $controller)
    {
        parent::__construct($controller);
        //$this->jquery->
    }

    public function listGroups(array $groups){
        $dt =$this->semantic->dataTable('dt-groups', Groups::class, $groups);
        //dt->setField(['name', 'email']);
        //$td->

    }


}
