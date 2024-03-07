<?php

namespace common\modules\cart\models\forms;

use Yii;
use yii\base\Model;

class LocationForm extends Model
{
    public $location;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['location'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'location' => Yii::t('app','Location'),
        ];
    }
}
