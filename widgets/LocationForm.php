<?php
    namespace common\modules\cart\widgets;

use common\modules\cart\models\forms\LocationForm as ModelsLocationForm;
use yii\base\Widget;
    
    /**
     * CodeForm is a widget to display code form based on model.
     */

    class LocationForm extends Widget
    {
        public function init()
        {
            parent::init();
        }

        public function run()
        {
            $model = new ModelsLocationForm();

            return $this->render('locationForm', [
                'model' => $model,
            ]);
        }
    }