<?php
use common\modules\cart\assets\CartAsset;

$this->title = Yii::t('frontend','Confirmation Payment');

CartAsset::register($this);
?>
<div id="cart-confirmation">
    <div id="confirmation-print" class="container my-4">
        <div class="row my-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center my-3">
                            <img src="/img/receipt-check-success.svg" class="h-50 d-inline-block mb-2" style="width: 100px;" alt="<?=Yii::t('frontend','Success Payment')?>">
                            <h1 class="mt-3"><?=Yii::t('app','Success Payment')?></h1>
                            <?php if(!$isCollector) : ?>
                                <p class="lead m-0"><?=Yii::t('app','Thank you for your payment. Here\'s your receipt, and a confirmation email has been sent.')?></p>
                            <?php else : ?>
                                <p class="lead m-0"><?=Yii::t('app','Thank you for your payment. Here\'s your receipt.')?></p>
                            <?php endif ?>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?=$this->render('_transaction', [
                    'transaction' => $transaction,
                    'isCollector' => $isCollector,
                ])?>
            </div>
        </div>
    </div>
</div>