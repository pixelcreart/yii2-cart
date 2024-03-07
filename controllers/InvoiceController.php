<?php
namespace common\modules\cart\controllers;

use Yii;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class InvoiceController extends Controller
{
    public $modelClass = 'common\models\Invoice';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index'  => ['get','post','delete'],
            ],
        ];
        
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];

        return $behaviors;
    }

    public function actionIndex(int $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        switch (Yii::$app->request->method) {
            case 'GET':
                return $this->methodGet($id);
            default:
                return [
                    'status' => 'error',
                    'message' => 'Method not allowed',
                ];
        }
    }

    public function methodGet($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if(!$id) {
            $model = $this->modelClass::find()->all();

            return [
                'status' => 'success',
                'data' => $model,
            ];
        } else {
            $model = $this->findModel($id);

            if ($model) {
                return [
                    'status' => 'success',
                    'data' => array_merge($model->toArray(), [
                        'expired' => $model->isExpired,
                        'overdue_fee' => $model->overdueFee,
                    ]),
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Invoice not found',
                ];
            }
        }
    }

    public function findModel($id)
    {
        if (($model = $this->modelClass::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}