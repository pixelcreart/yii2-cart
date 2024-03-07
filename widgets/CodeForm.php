<?php
    namespace common\modules\cart\widgets;

use common\modules\cart\models\forms\CodeForm as ModelsCodeForm;
use yii\base\Widget;
    
    /**
     * CodeForm is a widget to display code form based on model.
     */

    class CodeForm extends Widget
    {
        public function init()
        {
            parent::init();
        }

        public function run()
        {
            $model = new ModelsCodeForm();

            return $this->render('codeForm', [
                'model' => $model,
            ]);
        }
    }