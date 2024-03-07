<?php

namespace common\modules\cart\models\forms;

use Yii;
use yii\base\Model;

class CheckoutForm extends Model
{
    public $paymentMethodId;
    public $paymentMethodType;
    public $ccHolderName;
    public $ccHolderEmail;
    public $ccHolderIdentity;
    public $ccNumber;
    public $ccExpDate;
    public $ccCvv;
    public $serviceFee;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['paymentMethodId','paymentMethodType'], 'required'],
            [['paymentMethodId','paymentMethodType'], 'safe'],
            [['serviceFee'], 'number'],
            [['ccNumber','ccHolderName','ccHolderEmail','ccHolderIdentity','ccExpDate','ccCvv'], 'required',
            'when' => function($model) {
                return $model->paymentMethodType == 'credit_card';
            },
            'whenClient' => "function (attribute, value) {
                return $('#checkoutform-paymentmethodtype').val() == 'credit_card';
            }"],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'paymentMethodId' => Yii::t('app','Payment Method ID'),
            'paymentMethodType' => Yii::t('app','Payment Method Type'),
            'ccHolderName' => Yii::t('app','Card Holder Name'),
            'ccHolderEmail' => Yii::t('app','Card Holder Email'),
            'ccHolderIdentity' => Yii::t('app','Card Holder Identity'),
            'ccNumber' => Yii::t('app','Credit Card Number'),
            'ccExpDate' => Yii::t('app','Exp Date'),
            'ccCvv' => Yii::t('app','CVV'),
            'serviceFee' => Yii::t('app','Service Fee'),
        ];
    }
}
