<?php
use yii\helpers\Html;

/** @var yii\web\View $this */

$formatter = Yii::$app->formatter;

$cart->total_amount += $service_fee;
?>
<div class="card mb-3">
    <div class="card-header">
        <?=Yii::t('frontend','Summary')?>
    </div>
    <?php foreach($invoices as $inv) : ?>
        <div class="list-group list-group-flush">
            <div class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between mb-1">
                    <div class="fw-bold">
                        <?=$inv->descName?>
                    </div>
                    <div class="text-end">
                        <?=$formatter->asCurrency($inv->total_amount+$inv->overdueFee)?>
                    </div>
                </div>
                <?php foreach($inv->invoiceItems as $item) : ?>
                <div class="d-flex w-100 justify-content-between text-muted">
                    <div class="small">
                        <?=$item->description?>
                    </div>
                    <div class="small text-end">
                        <?=$formatter->asCurrency($item->amount)?>
                    </div>
                </div>
                <?php endforeach ?>
                <?php if($inv->expired) : ?>
                <div class="d-flex w-100 justify-content-between text-muted">
                    <div class="small">
                        <?=Yii::t('app','Overdue Charges')?>
                    </div>
                    <div class="small text-end">
                        <?=$formatter->asCurrency($inv->overdueFee)?>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>
    <?php if($service_fee > 0) : ?>
        <div class="list-group list-group-flush">
            <div class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <div class="fw-bold">
                        <?=Yii::t('app','Service Fee')?>
                    </div>
                    <div class="text-end">
                        <?=$formatter->asCurrency($service_fee)?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
    <div class="card-body text-bg-success">
        <div class="text-center">
            <div class="fs-4">Total a Pagar</div>
            <div class="fs-1 fw-bold"><?=$formatter->asCurrency($cart->total_amount)?></div>
        </div>
    </div>
    <div class="card-footer">
        <?=Html::a('<i class="bi bi-arrow-left me-3"></i>'.Yii::t('app','Cart'), '/cart', ['class' => 'btn btn-link text-decoration-none'])?>
    </div>
</div>