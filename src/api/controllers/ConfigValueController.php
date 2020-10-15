<?php

namespace ccheng\config\api\controllers;

use ccheng\config\api\models\forms\config_value\SaveForm;
use ccheng\config\api\models\searchs\ConfigValueSearch;
use ccheng\config\common\models\ConfigValue;

class ConfigValueController extends BaseController
{
    public $modelClass = ConfigValue::class;

    public function actionIndex()
    {
        $form = new ConfigValueSearch();
        $form->load($this->params, '');
        $list['list'] = $form->search();

        return $list;
    }

    public function actionSave(){
        $form = new SaveForm();
        $form->load($this->params, '');
        $form->save();
    }

}