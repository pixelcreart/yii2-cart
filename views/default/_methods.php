<?php
use common\widgets\Alert;
use yii\helpers\Html;

?>
<div class="card mb-3">
    <div class="card-header">
        <?=Yii::t('frontend','Payment Methods')?>
    </div>
    <?php if(count($paymentMethods) == 1) : ?>
        <?php
            $method = current($paymentMethods);

            $model->paymentMethodId = $method->id;
            $model->paymentMethodType = $method->payment_method_type;
        ?>

        <div class="card-body">
            <div class="alert-container">
                <?=Alert::widget()?>
            </div>
            <?=$this->render('payment-methods/_method-'.$method->payment_method_type, [
                'model' => $model,
                'method' => $method,
                'form' => $form,
            ])?>
        </div>
    <?php else : ?>
        <div class="card-body bg-light">
            <div class="alert-container">
                <?=Alert::widget()?>
            </div>
            <div class="text-center my-3">
                <img src="/img/shopping-cart.svg" class="col-1" alt="<?=Yii::t('frontend','Checkout')?>">
            </div>
            <p class="lead text-center"><?=Yii::t('frontend','Select any of the methods to make the payment.')?></p>
        </div>
        <div class="accordion accordion-flush" id="accordionPaymentMethods">
            <?php foreach($paymentMethods as $method) : ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-<?=$method->id?>" aria-expanded="false" aria-controls="flush-collapse-<?=$method->id?>">
                            <?=$method->name?>
                        </button>
                    </h2>
                    <div id="flush-collapse-<?=$method->id?>" class="accordion-collapse collapse" data-bs-parent="#accordionPaymentMethods" data-payment_method_id="<?=$method->id?>" data-payment_method_type="<?=$method->payment_method_type?>">
                        <div class="accordion-body">
                            <?=$this->render('payment-methods/_method-'.$method->payment_method_type, [
                                'model' => $model,
                                'method' => $method,
                                'form' => $form,
                            ])?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <div class="card-footer">
        <div class="row">
            <div class="col-12 text-center p-2">
                <div class="d-grid gap-2">
                    <?=Html::submitButton('Pagar', [ 'id' => 'checkout-submit', 'class' => 'btn btn-success btn-lg' ])?>
                </div>
            </div>
        </div>
    </div>
</div>