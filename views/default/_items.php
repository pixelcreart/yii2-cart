<?php
$formatter = Yii::$app->formatter;

$isExpired = $inv->isExpired;
?>
<a href="#!" class="list-group-item list-group-item-action">
    <div class="d-flex w-100 justify-content-between">
        <div class="mb-1"><input type="checkbox" name="items[]" id="<?=$counter?>" class="item d-none" value="<?=$inv->id?>" data-invoice_id="<?=$inv->id?>" data-invoice_date="<?=$inv->invoice_date?>" data-service_id="<?=$serviceId?>" data-invoice-identity="<?=$inv->invoice_number?>-<?=$inv->invoice_code?>" data-service-id="<?=$inv->subscription->service_id?>"> <label for="<?=$inv->id?>"><?=$inv->invoice_number?>-<?=$inv->invoice_code?> | <span class="fw-bold"><?=$formatter->asCurrency($inv->total_amount+$inv->overdueFee)?></span> <?=$isExpired ? '<span class="badge text-bg-danger ms-2">'.Yii::t('app','Overdue').'</span>' : ''?></label></div>
        <div class="small"><?=$formatter->asDate($inv->invoice_date)?></div>
    </div>
    <?php foreach($inv->invoiceItems as $item) : ?>
        <div class="d-flex w-100 justify-content-between">
            <div><?=$item->description?></div>
            <div><?=$formatter->asCurrency($item->amount)?></div>
        </div>
    <?php endforeach ?>
    <?php if($isExpired && $inv->overdueFee > 0) : ?>
        <div class="d-flex w-100 justify-content-between">
            <div><?=Yii::t('app','Overdue Charges')?></div>
            <div><?=$formatter->asCurrency($inv->overdueFee)?></div>
        </div>
    <?php endif ?>
</a>