<?php

namespace common\modules\cart\models;

use Yii;
use common\models\Invoice;
use common\models\PaymentMethod;
use common\models\Site;
use common\models\Subscription;
use common\models\Transaction;
use common\models\TransactionInvoice;
use common\models\TransactionNotification;
use yii\db\ActiveRecord;
use common\modules\user\models\User;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cart".
 *
 * @property int $id
 * @property float|null $subtotal_amount
 * @property float|null $tax_amount
 * @property float|null $total_amount
 * @property string|null $customer_code
 * @property string|null $session_id
 * @property string|null $user_ip
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string|null $items
 * @property float|null $service_fee_amount
 *
 * @property User $createdBy
 * @property Transaction[] $transactions
 * @property User $updatedBy
 */
class Cart extends \yii\db\ActiveRecord
{
    public const STATUS_EXPIRED = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_COMPLETED = 2;

    /**
     * Behaviors
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => '\yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return date("Y-m-d H:i:s");
                },
            ],
            'user' => [
                'class' => '\yii\behaviors\BlameableBehavior',
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subtotal_amount', 'tax_amount', 'total_amount', 'service_fee_amount'], 'number'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['items'], 'string'],
            [['customer_code', 'session_id', 'user_ip'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subtotal_amount' => Yii::t('app', 'Subtotal Amount'),
            'tax_amount' => Yii::t('app', 'Tax Amount'),
            'total_amount' => Yii::t('app', 'Total Amount'),
            'customer_code' => Yii::t('app', 'Customer Code'),
            'session_id' => Yii::t('app', 'Session ID'),
            'user_ip' => Yii::t('app', 'User Ip'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'items' => Yii::t('app', 'Items'),
            'service_fee_amount' => Yii::t('app', 'Service Fee Amount'),
        ];
    }

    /**
     * Register a cart
     */
    public static function register()
    {
        $session_id = Yii::$app->session->id;
        $user_ip = Yii::$app->request->userIP;

        $cart = new Cart();
        $cart->session_id = $session_id;
        $cart->user_ip = $user_ip;
        $cart->status = self::STATUS_ACTIVE;
        
        if(!$cart->save()) {
            Yii::$app->logtail->error('Cart not saved: '.__CLASS__, [
                'env' => YII_ENV,
                'session_id' => $session_id,
                'user_ip' => $user_ip,
                'errors' => $cart->errors,
            ]);

            return null;
        }

        return $cart;
    }

    /**
     * Find Cart by session_id and status active
     */
    public static function findBySessionId($session_id, int $status = self::STATUS_ACTIVE)
    {
        return self::find()
                ->where([
                    'session_id' => $session_id,
                    'status' => $status,
                ])
                ->one();
    }

    /**
     * Retrieve a cart
     */
    public static function retrieve($upsert = true)
    {
        $session_id = Yii::$app->session->id;

        // Select cart by session_id and status active
        $cart = self::findBySessionId($session_id);

        // If upsert is false will return cart or null
        if(!$upsert)
            return $cart;

        if(!$cart) {
            $cart = self::register();
        }

        return $cart;
    }

    /**
     * Process a cart
     */
    public static function process($checkout)
    {
        $serviceFee = 0;
        $cart = self::retrieve();
        
        $output = [
            'status' => false,
            'message' => '',
            'data' => [],
        ];

        if(empty($cart->items)) {
            $output['message'] = Yii::t('app','Cart is empty');
            $output['data'] = [
                'type' => 'cart_empty',
            ];
            return $output;
        }

        // check if user is collector
        if(Yii::$app->user->can('collector')) {
            $serviceFee = Yii::$app->user->collector->service_fee;
        } else {
            $serviceFee = $cart->service_fee_amount;
        }

        $conn = Yii::$app->db;

        $transaction = $conn->beginTransaction();

        // 1. Validate payment method
        // 1.2 Process payment method
        $paymentData = self::processPaymentMethod($checkout, $cart, $serviceFee);

        // 2. Create transactions
        $transactionData = self::createTransaction($checkout, $cart, $paymentData, $serviceFee);

        if(!$paymentData['status']) {
            $transaction->rollBack();

            return $paymentData;
        }

        if(!$transactionData['status']) {
            $transaction->rollBack();

            return $transactionData;
        }

        // 2.1 Update invoices status
        $invoices = self::invoiceProcess($cart,$paymentData);

        if(!$invoices['status']) {
            $transaction->rollBack();

            return $output;
        }

        // 5. Commit transaction
        $transaction->commit();

        $output = [
            'status' => true,
            'message' => Yii::t('app','Cart processed successfully'),
            'data' => [
                'cart' => $cart,
                'payment' => $paymentData,
                'transaction' => $transactionData,
                'invoices' => $invoices,
            ],
        ];

        // 6. Send email notification
        TransactionNotification::upsert($transactionData['data']['id'], TransactionNotification::TYPE_PAYMENT, Yii::$app->params['sendgrid']['templates']['purchaseConfirmation']);

        return $output;
    }

    /**
     * Complete a cart
     */
    public static function complete($upsert = true)
    {
        $cart = self::retrieve($upsert);
        $output = [
            'status' => true,
            'message' => '',
            'data' => [],
        ];

        if(!empty($cart)) {
            $cart->status = self::STATUS_COMPLETED;

            if(!$cart->save()) {
                $output['message'] = Yii::t('app','Cart not updated');
                $output['data'] = [
                    'errors' => $cart->errors,
                ];
                
                return $output;
            }
        }

        return $output;
    }

    /**
     * Process payment method
     */
    public static function processPaymentMethod($post, $cart, float $serviceFee = 0)
    {
        $formatter = Yii::$app->formatter;

        $output = [
            'status' => false,
            'message' => '',
            'data' => [],
        ];

        switch ($post->paymentMethodType) {
            case 'credit_card':
                $creditCardNumber = str_replace(' ', '', $post->ccNumber);
                $expDate = explode('/', $post->ccExpDate);

                $ccPayload = [
                    'comment' => Yii::$app->name.' | ' . $cart->customer_code,
                    'accountNumber' => $creditCardNumber,
                    'expirationYear' => $expDate[1],
                    'expirationMonth' => $expDate[0],
                    'cvc' => $post->ccCvv,
                    'cardHolderName' => $post->ccHolderName,
                    'customerEmail' => $post->ccHolderEmail,
                    'customerName' => $post->ccHolderName,
                    'amount' => $cart->total_amount+$serviceFee,
                    'taxes' => $cart->tax_amount,
                    'externalReference' => "{$cart->id}-{$cart->customer_code}-".date('YmdHis'),
                ];

                if(YII_DEBUG) {
                    Yii::$app->logtail->debug('Credit card payload: '.__CLASS__, [
                        'env' => YII_ENV,
                        'payload' => $ccPayload,
                    ]);
                }

                $paymentProcessor = Yii::$app->todoPago;
                $result = $paymentProcessor->payDirect($ccPayload);

                if($result['success']) {
                    $output['status'] = true;
                    $output['message'] = Yii::t('app','Payment successfully processed');
                    $output['data'] = [
                        'approved' => $result['success'],
                        'payment_method_type' => $post->paymentMethodType,
                        'payment_method_id' => $post->paymentMethodId,
                        'amount' => $cart->total_amount,
                        'service_fee' => $serviceFee,
                        'currency' => $formatter->currencyCode,
                        'status' => Transaction::STATUS_APPROVED,
                        'timestamp' => date("Y-m-d H:i:s"),
                        'comment' => '',
                        'transaction_id' => $result['data']['transaccionID'],
                        'processor_code' => $result['data']['processorCode'],
                        'bin_card' => $result['bin_card'],
                        'raw_response' => $result,
                    ];
                } else {
                    $output['status'] = $result['success'];
                    $output['message'] = $result['message'];
                    $output['data'] = [
                        'approved' => $result['success'],
                        'payment_method_type' => $post->paymentMethodType,
                        'payment_method_id' => $post->paymentMethodId,
                        'amount' => $cart->total_amount,
                        'service_fee' => $serviceFee,
                        'currency' => $formatter->currencyCode,
                        'status' => Transaction::STATUS_REJECTED,
                        'timestamp' => date("Y-m-d H:i:s"),
                        'comment' => '',
                        // 'transaction_id' => $result['data']['transaccionID'],
                        'bin_card' => $result['bin_card'],
                        'raw_response' => $result,
                    ];
                }
                break;
            case 'cash':
                $output['status'] = true;
                $output['message'] = Yii::t('app','Payment in cash');
                $output['data'] = [
                    'approved' => true,
                    'payment_method_type' => $post->paymentMethodType,
                    'payment_method_id' => $post->paymentMethodId,
                    'amount' => $cart->total_amount,
                    'service_fee' => $serviceFee,
                    'currency' => $formatter->currencyCode,
                    'status' => Transaction::STATUS_PENDING,
                    'timestamp' => date("Y-m-d H:i:s"),
                    'comment' => '',
                ];
                break;
            case 'bank':
                $output['status'] = true;
                $output['message'] = Yii::t('app','Payment in deposit or transfer to bank account');
                $output['data'] = [
                    'approved' => true,
                    'payment_method_type' => $post->paymentMethodType,
                    'payment_method_id' => $post->paymentMethodId,
                    'amount' => $cart->total_amount,
                    'service_fee' => $serviceFee,
                    'currency' => $formatter->currencyCode,
                    'status' => Transaction::STATUS_PENDING,
                    'timestamp' => date("Y-m-d H:i:s"),
                    'comment' => '',
                ];
                break;
            case 'credit_card':
                $paymentProcessor = Yii::$app->todoPago;

                $result = $paymentProcessor->payDirect();

                break;
            default:
                $output['message'] = Yii::t('app','Payment method not found');
                break;
        }

        return $output;
    }

    /**
     * Create transaction
     */
    public static function createTransaction($post,object $cart,array $paymentData, float $serviceFee = 0)
    {
        $mode = Yii::$app->getModule('cart')->mode;

        $output = [
            'status' => false,
            'message' => '',
            'data' => [],
        ];

        $model = new Transaction();

        $paymentData = $paymentData['data'];

        $subscription = Subscription::find()->andWhere(['customer_code' => $cart->customer_code])->one();

        switch($paymentData['payment_method_type']) {
            case 'credit_card':
                $model->transaction_type = Transaction::TYPE_CARD;
                $model->status = $paymentData['status'];
                $model->payer_name = $post->ccHolderName;
                $model->payer_email = $post->ccHolderEmail;
                $model->payer_document_id = $post->ccHolderIdentity;
                $model->authcode = isset($paymentData['processor_code']) ? $paymentData['processor_code'] : null;
                $model->transaction_id = isset($paymentData['transaction_id']) ? (string) $paymentData['transaction_id'] : null;
                $model->card_last_four = substr(str_replace(' ','',$post->ccNumber), -4);
                $model->confirm_at = date("Y-m-d H:i:s");
                $model->confirm_by = Yii::$app->user->id;
                $model->service_fee = $serviceFee;

                if($paymentData['bin_card']['valid']=='true') {
                    $model->card_bin = $paymentData['bin_card']['bin'];
                    $model->card_brand = $paymentData['bin_card']['card'];
                    $model->card_bank = $paymentData['bin_card']['bank'];
                    $model->card_type = $paymentData['bin_card']['type'];
                    $model->card_country = $paymentData['bin_card']['countrycode'];
                }

                break;
            case 'cash':
                $model->transaction_type = Transaction::TYPE_CASH;
                $model->payer_name = $subscription->affiliate->name;
                $model->payer_email = $subscription->affiliate->email;
                $model->payer_phone = $subscription->affiliate->affiliateAddresses[0]->phone_number;
                $model->payer_document_id = $subscription->affiliate->doc_value;
                $model->service_fee = $serviceFee;

                if($mode=='collector') {
                    $model->status = Transaction::STATUS_APPROVED;
                    $model->confirm_at = date("Y-m-d H:i:s");
                    $model->confirm_by = Yii::$app->user->id;
                    $model->commission_amount = $cart->total_amount * (Yii::$app->user->collector->commission/100);
                } else {
                    $model->status = Transaction::STATUS_PENDING;
                }

                break;
            case 'bank':
                $model->transaction_type = Transaction::TYPE_BANK;
                $model->status = Transaction::STATUS_PENDING;
                $model->payer_name = $subscription->affiliate->name;
                $model->payer_email = $subscription->affiliate->email;
                $model->payer_phone = $subscription->affiliate->affiliateAddresses[0]->phone_number;
                $model->payer_document_id = $subscription->affiliate->doc_value;
                $model->service_fee = $serviceFee;

                break;
            default:
                $model->transaction_type = Transaction::TYPE_CASH;
                $model->status = Transaction::STATUS_PENDING;

                break;
        }

        $model->amount = $cart->total_amount;
        $model->subtotal_amount = $cart->subtotal_amount;
        $model->raw_response = json_encode($paymentData);
        $model->cart_id = $cart->id;
        $model->payment_method_id = $paymentData['payment_method_id'];
        $model->collector_id = Yii::$app->user->getCollectorId();
        
        if($mode=='collector') {
            $model->site_id = Site::getSiteIdByCode($subscription->service->creditor->site->code);
        } else {
            $model->site_id = Site::getSiteIdByCode(Yii::$app->id);
        }
        
        if(!$model->save()) {
            Yii::$app->logtail->error('Transaction not saved: '.__CLASS__, [
                'errors' => $model->errors,
                'payload' => ArrayHelper::toArray($model),
            ]);
            $output['message'] = Yii::t('app','Transaction not saved');
            $output['errors'] = $model->errors;
        } else {
            $output['status'] = true;
            $output['message'] = Yii::t('app','Transaction saved');
            $output['data'] = $model;

            foreach(json_decode($cart->items) as $invoiceId) {
                $transactionInvoice = new TransactionInvoice();
                $transactionInvoice->transaction_id = $model->id;
                $transactionInvoice->invoice_id = $invoiceId;
                
                if(!$transactionInvoice->save()) {
                    $output['message'] = Yii::t('app','Transaction invoice not saved');
                    $output['errors'] = $transactionInvoice->errors;
                }
            }
        }

        return $output;
    }

    /**
     * Invoice process
     */
    public static function invoiceProcess($cart,$paymentMethodData)
    {
        $output = [
            'status' => false,
            'message' => '',
            'data' => [],
        ];

        if(Yii::$app->user->can('collector')) {
            $model = Invoice::updateAll(['status' => Invoice::STATUS_PAID], ['id' => json_decode($cart->items)]);
        } else  if(in_array($paymentMethodData['data']['payment_method_type'],[PaymentMethod::METHOD_TYPE_CASH,PaymentMethod::METHOD_TYPE_BANK])) {
            $model = Invoice::updateAll(['status' => Invoice::STATUS_PENDING_CONFIRMATION], ['id' => json_decode($cart->items)]);
        } else {
            $model = Invoice::updateAll(['status' => Invoice::STATUS_PAID], ['id' => json_decode($cart->items)]);
        }

        Invoice::updateOverdueInvoice(json_decode($cart->items));

        if(!$model) {
            Yii::$app->logtail->error('Invoices status not updated', [
                'payload' => ArrayHelper::toArray($cart),
            ]);
        }

        $output['status'] = true;
        $output['message'] = Yii::t('app','Invoices updated');
        $output['data'] = [
            'record_updated' => $model,
        ];
        
        return $output;
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Transactions]].
     *
     * @return \yii\db\ActiveQuery|TransactionQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['cart_id' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return CartQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CartQuery(get_called_class());
    }
}
