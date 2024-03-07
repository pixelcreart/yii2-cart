<?php
$this->title = Yii::t('frontend','Invoices');

$name = 'Guess';

$this->registerJsFile('@web/js/cart'.(YII_ENV_PROD ? '.min' : '').'.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/payment'.(YII_ENV_PROD ? '.min' : '').'.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/js/invoices'.(YII_ENV_PROD ? '.min' : '').'.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="search-invoices">
    <div class="container">
        <?=$this->render('../cart/_form', [
            'model' => $model,
        ])?>
    </div>
</div>