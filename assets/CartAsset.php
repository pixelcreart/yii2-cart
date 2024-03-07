<?php

namespace common\modules\cart\assets;

use common\assets\PrintAsset;
use Yii;
use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class CartAsset extends AssetBundle
{   
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];

    public function init()
    {
        parent::init();

        $this->css = [
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
        ];

        $this->js = [
            'https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/df-number-format/2.1.6/jquery.number.min.js',
            'https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js',
            [
                'src' => 'https://unpkg.com/cleave-zen@0.0.17/dist/cleave-zen.umd.js',
                'position' => \yii\web\View::POS_HEAD,
            ],
            YII_ENV_DEV ? 'js/cart/cart.js' : 'js/cart/cart.min.js?v='.Yii::$app->params['appVersion'],
            YII_ENV_DEV ? 'js/cart/payment.js' : 'js/cart/payment.min.js?v='.Yii::$app->params['appVersion'],
            YII_ENV_DEV ? 'js/cart/invoices.js' : 'js/cart/invoices.min.js?v='.Yii::$app->params['appVersion'],
            [
                'src' => YII_ENV_DEV ? 'js/cart/confirmation.js' : 'js/cart/confirmation.min.js?v='.Yii::$app->params['appVersion'],
                'depends' => [
                    PrintAsset::class,
                ],
            ],
        ];
    }
}
