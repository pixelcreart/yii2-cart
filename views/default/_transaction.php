<?php
    use common\models\Transaction;
    use common\widgets\FieldValue;
    use yii\helpers\Html;

    $formatter = Yii::$app->formatter;
?>
<div class="card card-header-actions">
    <div class="card-header">
        <div><i class="bi bi-receipt"></i> <?=Yii::t('app','Payment Receipt')?></div>
        <div>
            <?php if($isCollector) : ?>
                <?= Html::a('<i class="fa fa-print"></i>', ['print-receipt', 'code' => $transaction->transaction_id], [
                    'class' => 'btn btn-primary btn-icon mr-2',
                    'target' => '_blank',
                ]) ?>
            <?php endif ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-7 mb-3">
                <div class="list-group">
                    <?php foreach($transaction->invoices as $invoice) : ?>
                        <div class="list-group-item border-bottom py-3">
                            <div class="d-flex w-100 justify-content-between mb-2">
                                <div>
                                    <h5 class="mb-1"><?=$invoice->subscription->service->creditor->name?> | <?=$invoice->subscription->service->name?></h5>
                                    <small><?=Yii::t('app','Subscription Code: {code}', [
                                        'code' => $invoice->subscription->customer_code,
                                    ])?></small>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div><?=$invoice->subscription->affiliate->name?></div>
                                <?php foreach($invoice->subscription->affiliate->affiliateAddresses as $address) : ?>
                                    <small><?=$address->address_1?>, <?=$address->address_2?>, <?=$address->city_name?>, <?=$address->country->name?></small>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-flex w-100 justify-content-between mb-1">
                                <div class="mb-1"><?=$invoice->invoice_number?>-<?=$invoice->invoice_code?></div>
                                <div><?=$formatter->asCurrency($invoice->total_amount)?></div>
                            </div>
                            <?php foreach($invoice->invoiceItems as $item) : ?>
                                <div class="d-flex w-100 justify-content-between small">
                                    <div><?=$item->description?></div>
                                    <div><?=$formatter->asCurrency($item->amount)?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-5 mb-3">
                <?=FieldValue::widget([
                    'field' => Yii::t('app','Payment Method'),
                    'value' => $transaction->paymentMethod->name,
                ])?>
                <?php if($transaction->service_fee > 0) : ?>
                    <?=FieldValue::widget([
                        'field' => Yii::t('app','Service Fee'),
                        'value' => $transaction->service_fee,
                        'type' => 'currency',
                    ])?>
                <?php endif; ?>
                <?=FieldValue::widget([
                    'field' => Yii::t('app','Amount Paid'),
                    'value' => $transaction->amount+$transaction->service_fee,
                    'type' => 'currency',
                ])?>
                <?=FieldValue::widget([
                    'field' => Yii::t('app','Date'),
                    'value' => $transaction->created_at,
                    'type' => 'datetime',
                ])?>
                <?php if($transaction->transaction_type == Transaction::TYPE_CARD) : ?>
                    <?=FieldValue::widget([
                        'field' => Yii::t('app','Card Name'),
                        'value' => $transaction->payer_name,
                    ])?>
                    <?=FieldValue::widget([
                        'field' => Yii::t('app','Card Used'),
                        'value' => '**** **** **** '.$transaction->card_last_four,
                    ])?>
                    <?=FieldValue::widget([
                        'field' => Yii::t('app','Authorization Code'),
                        'value' => $transaction->authcode,
                    ])?>
                <?php endif; ?>
                <?=FieldValue::widget([
                    'field' => Yii::t('app','Confirmation Code'),
                    'value' => $transaction->shortTransactionId,
                ])?>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <?php if(Yii::$app->user->isGuest) : ?>
            <?=Html::a('<i class="bi bi-arrow-left me-2"></i> '.Yii::t('app','Make another payment'), ['/'], [
                'class' => 'btn btn-link text-decoration-none',
            ])?>
        <?php elseif(Yii::$app->user->can('collector')) : ?>
            <?=Html::a('<i class="bi bi-arrow-left me-2"></i> '.Yii::t('app','Dashboard'), ['/'], [
                'class' => 'btn btn-link text-decoration-none',
            ])?>
        <?php else : ?>
            <?=Html::a('<i class="bi bi-arrow-left me-2"></i> '.Yii::t('app','Dashboard'), ['/app'], [
                'class' => 'btn btn-link text-decoration-none',
            ])?>
        <?php endif ?>
    </div>
</div>