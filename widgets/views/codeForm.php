<?php
use common\widgets\Alert;
use kartik\form\ActiveForm;
?>
<div class="card rounded-3 text-dark">
    <div class="card-header py-4">
        <?=Yii::t('app','Pay Your Bills')?>
    </div>
    <div class="card-body">
        <?=Alert::widget()?>
        <?php $form = ActiveForm::begin([
            'id' => 'cart-code-form',
            'method' => 'get',
            'action' => ['/cart'],
        ]); ?>
        <div class="mb-3">
            <?=$form->field($model, 'code')->textInput(['placeholder' => Yii::t('app','Enter your code')])->label(false)?>
        </div>
        <div class="d-grid">
            <button class="btn btn-primary" type="submit"><?=Yii::t('app','Check')?></button>
        </div>
        <?php ActiveForm::end(); ?>        
    </div>
</div>