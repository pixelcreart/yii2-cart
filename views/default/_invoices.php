<?php
use common\models\Invoice;
use common\widgets\Alert;
use kartik\form\ActiveForm;

$formatter = Yii::$app->formatter;
$cookies = Yii::$app->request->cookies;
?>

<?php
 $form = ActiveForm::begin([
    'id' => 'payment-form',
]); ?>
<div class="row py-4"> 
    <?php
        if(count($subscriptions)) {
            $name = explode(' ', $affiliate->name)[0];
        } else {
            $name = Yii::t('app','Guess');
        }
    ?>
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <h1 class="mt-3"><?=Yii::t('app','Welcome, {name}', [
                    'name' => $name,
                ])?></h1>
                <p class="lead"><?=Yii::t('app','Select which invoices you want to pay, starting with older invoices before newer ones.')?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-receipt"></i> <?=Yii::t('app','Pending Invoices')?>
            </div>
            <div id="item-list" class="card-body">
                <?=Alert::widget()?>
                <?php $subsCounter = 0; ?>
                <?php $invCounter = 0; ?>
                <?php foreach($subscriptions as $subs) : ?>
                    <?php
                        $pendingInvoices = Invoice::getPendingInvoices($subs->id);
                    ?>
                        <?php if(!$pendingInvoices) continue; ?>
                    
                        <h5 class="card-title"><?=$subs->service->creditor->name?> <span class="text-gray-400">|</span> <?=$subs->service->name?></h5>
                        <div id="service-<?=$subs->service->id?>" class="list-group mb-4" data-service_fee_amount="<?=$subs->service->service_fee?>" data-service_fee_type="<?=$subs->service->service_fee_type?>">
                            <?php $invCounter = 0; ?>
                            <?php foreach($pendingInvoices as $inv) : ?>
                                <?=$this->render('_items', [
                                    'inv' => $inv,
                                    'serviceId' => $subs->service->id,
                                    'counter' => $invCounter,
                                ])?>
                                <?php $invCounter++?>
                            <?php endforeach ?>
                        </div>
                        <?php $subsCounter++; ?>
                <?php endforeach ?>

                <?php if(!$invCounter) : ?>
                    <div class="alert alert-info">
                        <div class="text-center">
                            <span class="fs-4"><i class="bi bi-check2-all text-muted"></i> <?=Yii::t('app','No pending invoices found')?></span>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?=$this->render('_widget', [
            'model' => $subscriptions,
            'customer_code' => $customer_code,
        ])?>
    </div>
</div>
<?php ActiveForm::end(); ?>
