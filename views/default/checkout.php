<?php

use common\modules\cart\assets\CartAsset;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = Yii::t('frontend','Checkout');

$formatter = Yii::$app->formatter;

$this->registerJsVar('paymentMethodAlertMessage', Yii::t('frontend','Please select a payment method.'));

CartAsset::register($this);

$this->registerJsFile(YII_ENV_PROD ? '@web/js/cart/checkout.min.js' : '@web/js/cart/checkout.js', [
    'depends' => [
        \yii\web\JqueryAsset::class,
    ],
])
?>
<div id="cart-checkout">
    <div class="container py-4">
        <?php $form = ActiveForm::begin([
            'id' => 'checkout-form',
            'action' => 'payment',
            'type' => ActiveForm::TYPE_FLOATING,
        ]); ?>
            <div class="row">
                <div class="col-md-6">
                    <?=$this->render('_summary', [
                        'invoices' => $invoices,
                        'cart' => $cart,
                        'service_fee' => $serviceFee,
                    ])?>
                </div>
                <div class="col-md-6">
                    <?php if($mode) : ?>
                        <?=$this->render('_collector-checkout.php')?>
                    <?php else : ?>
                        <?=$this->render('_methods', [
                            'paymentMethods' => $paymentMethods,
                            'form' => $form,                  
                            'model' => $model,
                        ])?>
                    <?php endif; ?>
                </div>
            </div>
            <?=Html::activeHiddenInput($model, 'paymentMethodId')?>
            <?=Html::activeHiddenInput($model, 'paymentMethodType')?>
        <?php ActiveForm::end(); ?>
    </div>
</div>