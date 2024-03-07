<?php
use yii\helpers\Html;
?>
<div class="row my-3">
    <div class="col-12 text-center">
        <div><img src="/img/coin-share.svg" alt="<?=Yii::t('frontend','Cash')?>" width="64" class="mb-3"></div>
        <p class="lead"><?=Yii::t('frontend','You must make the payment to a collection agent')?></p>
        
        <?php if(!empty($model->comment)) : ?>
            <p class="small"><?=$model->comment?></p>
        <?php endif; ?>
    </div>
</div>