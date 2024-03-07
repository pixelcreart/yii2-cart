<?php

use common\modules\cart\assets\CartAsset;

$this->title = Yii::t('frontend','Invoices');

$name = 'Guess';

$itemsSelected = empty($cart->items) ? [] : json_decode($cart->items);

$this->registerJsVar('itemsSelected', $itemsSelected);

$collectorServiceFee = Yii::$app->user->collector;

$this->registerJsVar('collectorServiceFee', Yii::$app->user->can('collector') ? $collectorServiceFee->service_fee : 0);

CartAsset::register($this);
?>

<div class="cart-default-index">
    <div class="main-content container">
        <?php if(!empty($customer_code) || !empty($location)) : ?>
            <?=$this->render('_invoices', [
                'customer_code' => $customer_code,
                'subscriptions' => $subscriptions,
                'affiliate' => $affiliate,
            ])?>
        <?php else : ?>
            <?=$this->render('_empty')?>
        <?php endif ?>
    </div>
</div>