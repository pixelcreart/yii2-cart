<?php

namespace common\modules\cart;

use Yii;

/**
 * cart module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'common\modules\cart\controllers';

    public $mode = 'default';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        Yii::setAlias('@cart', dirname(__DIR__).'/cart');
    }
}
