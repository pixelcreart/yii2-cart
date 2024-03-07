<?php
/* @var $this yii\web\View */

$this->registerJsFile(YII_ENV_DEV ? '@web/js/cart/credit-card.js' : '@web/js/cart/credit-card.min.js', [
    'position' => $this::POS_END,
]);
?>
<div class="row my-3">
    <div class="col-12 text-center">
        <div><img src="/img/credit-card.svg" alt="<?=Yii::t('frontend','Credit Card')?>" width="64" class="mb-3"></div>
        
        <div class="row">
            <div class="col-12">
                <?=$form->field($model, 'ccHolderName')->textInput(['maxlength' => 255])?>
            </div>
            <div class="col-12">
                <?=$form->field($model, 'ccHolderEmail')->textInput(['maxlength' => 255])?>
            </div>
            <div class="col-12">
                <?=$form->field($model, 'ccHolderIdentity')->textInput(['maxlength' => 16])?>
            </div>
            <div class="col-12">
                <?=$form->field($model, 'ccNumber')->textInput(['maxlength' => 20])?>
            </div>
            <div class="col-6">
                <?=$form->field($model, 'ccExpDate')->textInput(['maxlength' => 255])?>
            </div>
            <div class="col-6">
                <?=$form->field($model, 'ccCvv')->textInput(['maxlength' => 4])?>
            </div>
        </div>

        <?php if(!empty($model->comment)) : ?>
            <p class="small"><?=$model->comment?></p>
        <?php endif; ?>
    </div>
</div>