<?php
namespace common\modules\cart\controllers;

use common\models\Affiliate;
use common\models\Invoice;
use common\models\PaymentMethod;
use common\models\Site;
use common\models\Subscription;
use common\models\Transaction;
use common\modules\cart\models\Cart;
use common\modules\cart\models\forms\CheckoutForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Cookie;

/**
 * Default controller for the `cart` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex(string $code = null, array $CodeForm = [])
    {
        $post = Yii::$app->request->post();
        $params = Yii::$app->request->queryParams;

        $location = null;

        $siteCode = Yii::$app->id;

        // Get site code if app is manager
        if(Yii::$app->id=='manager') {
            $siteCode = Yii::$app->session->get('site')['code'];
        }

        // Process request from POST data
        if($post) {
            // Validate if items are empty or not set
            if(!(isset($post['items']) || empty($post['items']))) {
                Yii::$app->session->setFlash('warning', Yii::t('app', 'You need add items to cart'));

                return $this->redirect(['/cart']);
            }

            // Retrieve cart data from database
            $cart = Cart::retrieve();

            // Set cart data
            $cart->items = json_encode($post['items']);
            $cart->customer_code = $post['customer_code'];

            // Process invoices to get sums and other data
            $invoiceQuery = Invoice::find()->where(['id' => $post['items']]);

            $totalAmount = $invoiceQuery->sum('total_amount');
            $subtotalAmount = $invoiceQuery->sum('subtotal_amount');
            $taxAmount = $invoiceQuery->sum('total_tax_amount');

            // Iterate over invoices to get overdue fees
            foreach($invoiceQuery->all() as $invoice) {
                if($invoice->isExpired) {
                    $totalAmount += $invoice->overdueFee;
                }
            }

            $cart->total_amount = $totalAmount;
            $cart->subtotal_amount = $subtotalAmount;
            $cart->tax_amount = $taxAmount;
            $cart->service_fee_amount = $post['service_fee'];

            // Save cart data
            if(!$cart->save()) {
                Yii::$app->logtail->error('An error occurred while saving the cart', [
                    'cart' => ArrayHelper::toArray($cart),
                    'errors' => $cart->errors,
                ]);

                Yii::$app->session->setFlash('warning', Yii::t('app', 'An error occurred while saving the cart'));

                return $this->redirect(['/cart']);
            }
            
            if(YII_DEBUG) {
                Yii::$app->logtail->debug('Cart data saved', [
                    'cart' => ArrayHelper::toArray($cart),
                    'session_id' => Yii::$app->session->id,
                    'app_name' => Yii::$app->name,
                    'site_code' => $siteCode,
                ]);
            }

            // Redirect to checkout page
            return $this->redirect(['checkout']);
        }

        // Get site id
        $siteCode = Site::getSiteIdByCode($siteCode);

        // Init cart with this session id
        $cart = Cart::retrieve();

        if(isset($params['LocationForm']['location'])) {
            $location = $params['LocationForm']['location'];
        }

        if(!empty($CodeForm))
            $code = $CodeForm['code'];
            
        if(empty($code)) {
            $code = $cart->customer_code;
        }

        if(!empty($code)) {
            // Select subscriptions
            $subscriptions = Subscription::findByCustomerCode($code,$siteCode);
        } else if(!empty($location)) {
            // Select subscriptions
            $subscriptions = Subscription::findByLocation($location,$siteCode);
        } else {
            if(empty($code)) {
                Yii::$app->session->setFlash('warning', Yii::t('app', 'You need to provide a customer code or location'));
    
                return $this->render('index');
            }

        }

        if(!$subscriptions) {
            Yii::$app->session->setFlash('warning', Yii::t('app', 'No active subscriptions found for this customer code'));

            return $this->redirect(['/cart']);
        }

        // Get affiliate from subscription
        $affiliate = Affiliate::findOne(['id' => current($subscriptions)->affiliate_id]);
        
        return $this->render('index', [
            'cart' => $cart,
            'affiliate' => $affiliate,
            'subscriptions' => $subscriptions,
            'customer_code' => $code,
            'location' => $location,
        ]);
    }

    /**
     * Renders the checkout view for the module
     */
    public function actionCheckout()
    {
        $model = new CheckoutForm();
        $cart = Cart::retrieve();
        $mode = $this->module->mode=='collector';
        $serviceFee = 0;

        if(!$cart->items) {
            Yii::$app->session->setFlash('warning', Yii::t('app', 'You need add items to cart'));

            return $this->redirect(['/cart']);
        }

        if($mode=='collector') {
            $paymentMethods = [];
            $model->paymentMethodId = PaymentMethod::PAYMENT_METHOD_COLLECTOR;
            $model->paymentMethodType = PaymentMethod::METHOD_TYPE_CASH;
            $serviceFee = Yii::$app->user->collector->service_fee;
        } else {
            $serviceFee = $cart->service_fee_amount;
            $paymentMethods = PaymentMethod::find()->public()->active()->all();
        }

        $invoices = Invoice::find()->where(['id' => json_decode($cart->items)])->all();

        return $this->render('checkout', [
            'model' => $model,
            'cart' => $cart,
            'paymentMethods' => $paymentMethods,
            'invoices' => $invoices,
            'mode' => $mode,
            'serviceFee' => $serviceFee,
        ]);
    }

    /**
     * Process payment
     */
    public function actionPayment()
    {
        $post = Yii::$app->request->post();
        $model = new CheckoutForm();

        if($post) {
            $model->load($post);

            if(!$model->validate()) {
                Yii::$app->logtail->error('Errors were found in checkout form', [
                    'errors' => $model->errors,
                    'session_Id' => Yii::$app->session->id,
                    'app_name' => Yii::$app->name,
                ]);

                Yii::$app->session->setFlash('warning', Yii::t('app', 'Errors were found in the form'));

                return $this->redirect(['checkout']);
            }

            $cart = Cart::process($model);

            if(!$cart['status']) {
                Yii::$app->session->setFlash('warning', Yii::t('app', $cart['message']));

                return $this->redirect(['checkout']);
            }

            return $this->redirect(['confirmation', 'code' => $cart['data']['transaction']['data']['transaction_id']]);
        }

        Yii::$app->session->setFlash('warning', Yii::t('app', 'You need select a payment method'));

        return $this->redirect(['checkout']);
    }

    public function actionConfirmation(string $code)
    {
        $cart = Cart::complete(false);
        $isCollector = $this->module->mode=='collector';

        if(!$cart['status']) {
            Yii::$app->session->setFlash('warning', Yii::t('app', $cart['message']));

            return $this->redirect(['/cart']);
        }

        $transaction = Transaction::find()->where('transaction_id = :code', [
            ':code' => $code,
        ])->one();

        return $this->render('confirmation', [
            'transaction' => $transaction,
            'isCollector' => $isCollector,
        ]);
    }

    /**
     * Print receipt
     */
    public function actionPrintReceipt(string $code) {
        $this->layout = 'receipt';
        $model = Transaction::findOne(['transaction_id' => $code]);

        return $this->render('receipt',[
            'model' => $model
        ]);
    }
}
