<?php

use common\models\BankAccount;
use yii\helpers\Html;

/* @var $this yii\web\View */

$bankAccounts = BankAccount::find()->active()->all();
?>
<div class="row my-3">
    <div class="col-12 text-center">
        <div><img src="/img/coin-share.svg" alt="<?=Yii::t('frontend','Cash')?>" width="64" class="mb-3"></div>
        <p class="lead"><?=Yii::t('frontend','Please make a deposit or transfer to any of our banks accounts')?></p>
        <div class="text-start col-md-8 offset-md-2">
            <ul class="list-unstyled">
                <?php foreach($bankAccounts as $bankAccount) : ?>
                    <li><?=$bankAccount->name?>: <?=$bankAccount->account_number?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if(!empty($model->comment)) : ?>
            <div class="alert alert-info">
                <p class="small text-start m-o"><i class="bi bi-info"></i> <?=$model->comment?></p>
            </div>
        <?php endif; ?>
    </div>
</div>