<?php

namespace common\modules\cart\models\forms;

use Yii;
use yii\base\Model;

class CodeForm extends Model
{
    public $code;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app','Code'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'code' => '<i class="fas fa-info-circle me-1"></i>'. Yii::t('app','This code is provided by your service provider for your use.'),
        ];
    }
}
