<?php
namespace controllers\crud\datas;

use Ubiquity\controllers\crud\CRUDDatas;
 /**
  * Class CrudOrgasDatas
  */
class CrudOrgasDatas extends CRUDDatas{
	//use override/implement Methods

    public function getFieldNames($model)
    {
        return ['name', 'domain'];
    }


}



