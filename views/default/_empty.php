<?php
use yii\helpers\Html;
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center my-10">
            <i class="fas fa-shopping-cart fa-4x my-3 text-muted"></i>
            <h1 class="display-5"><?=Yii::t('app','Cart is empty')?></h1>
            <?php if(Yii::$app->user->isGuest) : ?>
                <?=Html::a('<i class="fas fa-arrow-left me-2"></i>'.Yii::t('app','Return'), [
                    '/'
                ])?>
            <?php else : ?>
                <?=Html::a('<i class="fas fa-arrow-left me-2"></i>'.Yii::t('app','Return to Dashboard'), [
                    '/app'
                ])?>
            <?php endif ?>
        </div>
    </div>
</div>