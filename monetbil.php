<?php

/*
  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License or any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Monetbil extends PaymentModule
{

    const GATEWAY = 'monetbil';
    const WIDGET_URL = 'https://www.monetbil.com/widget/';
    const GET_SERVICE_URL = 'https://api.monetbil.com/v1/services/get';
    const CHECK_PAYMENT_URL = 'https://api.monetbil.com/payment/v1/checkPayment';
    const MONETBIL_OS_SUCCESS_PAYMENT = 'MONETBIL_OS_SUCCESS_PAYMENT';
    const MONETBIL_OS_SUCCESS_PAYMENT_TESTMODE = 'MONETBIL_OS_SUCCESS_PAYMENT_TESTMODE';
    const MONETBIL_OS_FAILED_PAYMENT = 'MONETBIL_OS_FAILED_PAYMENT';
    const MONETBIL_OS_FAILED_PAYMENT_TESTMODE = 'MONETBIL_OS_FAILED_PAYMENT_TESTMODE';
    const MONETBIL_OS_CANCELLED_PAYMENT = 'MONETBIL_OS_CANCELLED_PAYMENT';
    const MONETBIL_OS_CANCELLED_PAYMENT_TESTMODE = 'MONETBIL_OS_CANCELLED_PAYMENT_TESTMODE';
    // Monetbil Service
    const MONETBIL_SERVICE_KEY = 'MONETBIL_SERVICE_KEY';
    const MONETBIL_SERVICE_SECRET = 'MONETBIL_SERVICE_SECRET';
    const MONETBIL_MERCHANT_NAME = 'MONETBIL_MERCHANT_NAME';
    const MONETBIL_MERCHANT_EMAIL = 'MONETBIL_MERCHANT_EMAIL';
    const MONETBIL_SERVICE_NAME = 'MONETBIL_SERVICE_NAME';
    const MONETBIL_PAYMENT_TITLE = 'MONETBIL_PAYMENT_TITLE';
    const MONETBIL_PAYMENT_DESCRIPTION = 'MONETBIL_PAYMENT_DESCRIPTION';
    // Monetbil Widget version
    const MONETBIL_WIDGET_DEFAULT_VERSION = 'v2';
    const MONETBIL_WIDGET_VERSION_V1 = 'v1';
    const MONETBIL_WIDGET_VERSION_V2 = 'v2';
    const MONETBIL_WIDGET_VERSION = 'MONETBIL_WIDGET_VERSION';
    // Live mode
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 0;
    const STATUS_CANCELLED = -1;
    // Test mode
    const STATUS_SUCCESS_TESTMODE = 7;
    const STATUS_FAILED_TESTMODE = 8;
    const STATUS_CANCELLED_TESTMODE = 9;

    public function __construct()
    {
        $this->name = 'monetbil';
        $this->tab = 'payments_gateways';
        $this->version = '1.10';
        $this->module_key = '';
        $this->is_eu_compatible = 1;
        $this->author = 'Serge NTONG';

        parent::__construct();

        $this->displayName = $this->l('Monetbil');
        $this->description = $this->l('A Payment Gateway for Mobile Money Payments - Prestashop');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
    }

    public function install()
    {
        if (!parent::install()
                or ! $this->registerHook('displayPayment')
                or ! $this->registerHook('payment')
                or ! $this->registerHook('paymentReturn')
                or ! $this->registerHook('displayPaymentEU')

                or ! $this->registerHook('displayHeader')
                or ! $this->registerHook('header')

                or ! $this->registerHook('displayfooter')
                or ! $this->registerHook('Footer')

                or ! $this->registerHook('backOfficeHeader')
                or ! $this->registerHook('displayBackOfficeHeader')

                or ! $this->registerHook('displayBackOfficeFooter')
        ) {
            return false;
        }

        // Create Order States
        $this->addOrderStates(Monetbil::MONETBIL_OS_SUCCESS_PAYMENT, 'Monetbil Successful payment', '#086fd1', 'payment', true, true, true, true);
        $this->addOrderStates(Monetbil::MONETBIL_OS_SUCCESS_PAYMENT_TESTMODE, 'Monetbil Successful payment - TESTMODE', '#086fd1', '', false, false, false, false);

        $this->addOrderStates(Monetbil::MONETBIL_OS_FAILED_PAYMENT, 'Monetbil Payment failed', '#086fd1', '', false, false, false, false);
        $this->addOrderStates(Monetbil::MONETBIL_OS_FAILED_PAYMENT_TESTMODE, 'Monetbil Payment failed - TESTMODE', '#086fd1', '', false, false, false, false);

        $this->addOrderStates(Monetbil::MONETBIL_OS_CANCELLED_PAYMENT, 'Monetbil Transaction cancelled', '#086fd1', '', false, false, false, false);
        $this->addOrderStates(Monetbil::MONETBIL_OS_CANCELLED_PAYMENT_TESTMODE, 'Monetbil Transaction cancelled - TESTMODE', '#086fd1', '', false, false, false, false);

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
                or ! $this->unregisterHook('displayPayment')
                or ! $this->unregisterHook('payment')
                or ! $this->unregisterHook('paymentReturn')
                or ! $this->unregisterHook('displayPaymentEU')

                or ! $this->unregisterHook('displayHeader')
                or ! $this->unregisterHook('header')

                or ! $this->unregisterHook('displayfooter')
                or ! $this->unregisterHook('Footer')

                or ! $this->unregisterHook('backOfficeHeader')
                or ! $this->unregisterHook('displayBackOfficeHeader')

                or ! $this->unregisterHook('displayBackOfficeFooter')
        ) {
            return false;
        }

        (new OrderState(Configuration::get(Monetbil::MONETBIL_OS_SUCCESS_PAYMENT)))->delete();
        (new OrderState(Configuration::get(Monetbil::MONETBIL_OS_SUCCESS_PAYMENT_TESTMODE)))->delete();

        (new OrderState(Configuration::get(Monetbil::MONETBIL_OS_FAILED_PAYMENT)))->delete();
        (new OrderState(Configuration::get(Monetbil::MONETBIL_OS_FAILED_PAYMENT_TESTMODE)))->delete();

        (new OrderState(Configuration::get(Monetbil::MONETBIL_OS_CANCELLED_PAYMENT)))->delete();
        (new OrderState(Configuration::get(Monetbil::MONETBIL_OS_CANCELLED_PAYMENT_TESTMODE)))->delete();

        /* Clean configuration table */
        Configuration::deleteByName(Monetbil::MONETBIL_MERCHANT_NAME);
        Configuration::deleteByName(Monetbil::MONETBIL_MERCHANT_EMAIL);

        Configuration::deleteByName(Monetbil::MONETBIL_SERVICE_KEY);
        Configuration::deleteByName(Monetbil::MONETBIL_SERVICE_SECRET);
        Configuration::deleteByName(Monetbil::MONETBIL_SERVICE_NAME);

        Configuration::deleteByName(Monetbil::MONETBIL_PAYMENT_TITLE);
        Configuration::deleteByName(Monetbil::MONETBIL_PAYMENT_DESCRIPTION);
        Configuration::deleteByName(Monetbil::MONETBIL_WIDGET_VERSION);

        Configuration::deleteByName(Monetbil::MONETBIL_OS_SUCCESS_PAYMENT);
        Configuration::deleteByName(Monetbil::MONETBIL_OS_SUCCESS_PAYMENT_TESTMODE);

        Configuration::deleteByName(Monetbil::MONETBIL_OS_FAILED_PAYMENT);
        Configuration::deleteByName(Monetbil::MONETBIL_OS_FAILED_PAYMENT_TESTMODE);

        Configuration::deleteByName(Monetbil::MONETBIL_OS_CANCELLED_PAYMENT);
        Configuration::deleteByName(Monetbil::MONETBIL_OS_CANCELLED_PAYMENT_TESTMODE);

        return true;
    }

    /**
     * This function creates the states
     * for the order. Needed for
     * order creation and updates.
     */
    private function addOrderStates($key, $name, $color, $template, $invoice, $send_email, $paid, $logable)
    {
        // Create a new Order state if not already done
        if (!(Configuration::get($key) > 0)) {
            // Create a new state
            // and set the state
            // as Open

            $orderState = new OrderState(null, Configuration::get('PS_LANG_DEFAULT'));

            $orderState->name = $this->l($name);
            $orderState->invoice = $invoice;
            $orderState->send_email = $send_email;
            $orderState->module_name = $this->name;
            $orderState->color = $color;
            $orderState->unremovable = true;
            $orderState->hidden = false;
            $orderState->logable = $logable;
            $orderState->delivery = false;
            $orderState->shipped = false;
            $orderState->paid = $paid;
            $orderState->deleted = false;
            $orderState->template = $template;
            $orderState->add();

            // Update the value
            // in the configuration database
            Configuration::updateValue($key, $orderState->id);

            // Create an icon
            if (file_exists(dirname(__FILE__) . '/assets/img/os/logo_white_16.gif')) {
                copy(dirname(__FILE__)
                        . '/assets/img/os/logo_white_16.gif', dirname(dirname(dirname(__FILE__)))
                        . '/assets/img/os/' . $orderState->id . '.gif');
            }
        }
    }

    /**
     * Configuration content
     */
    public function getContent()
    {
        $title = Monetbil::getPost(Monetbil::MONETBIL_PAYMENT_TITLE, $this->l('Monetbil (Mobile Money)'));
        $description = Monetbil::getPost(Monetbil::MONETBIL_PAYMENT_DESCRIPTION, $this->l('Pay safely using your Mobile Money account.'));
        $version = Monetbil::getPost(Monetbil::MONETBIL_WIDGET_VERSION, Monetbil::MONETBIL_WIDGET_DEFAULT_VERSION);

        if (Tools::isSubmit('MonetbilServcieConfig')) {
            $service_key = Monetbil::getPost(Monetbil::MONETBIL_SERVICE_KEY);
            $service_secret = Monetbil::getPost(Monetbil::MONETBIL_SERVICE_SECRET);

            $service = Monetbil::getService($service_key, $service_secret);

            if (array_key_exists('service_key', $service)
                    and array_key_exists('service_secret', $service)
                    and array_key_exists('service_name', $service)
                    and array_key_exists('Merchants', $service)
            ) {
                Configuration::updateValue(Monetbil::MONETBIL_MERCHANT_NAME, $service['Merchants']['first_name'] . ' ' . $service['Merchants']['last_name']);
                Configuration::updateValue(Monetbil::MONETBIL_MERCHANT_EMAIL, $service['Merchants']['email']);
                Configuration::updateValue(Monetbil::MONETBIL_SERVICE_KEY, $service['service_key']);
                Configuration::updateValue(Monetbil::MONETBIL_SERVICE_SECRET, $service['service_secret']);
                Configuration::updateValue(Monetbil::MONETBIL_SERVICE_NAME, $service['service_name']);
            }
        }

        Configuration::updateValue(Monetbil::MONETBIL_PAYMENT_TITLE, $title);
        Configuration::updateValue(Monetbil::MONETBIL_PAYMENT_DESCRIPTION, $description);
        Configuration::updateValue(Monetbil::MONETBIL_WIDGET_VERSION, $version);

        $monetbil_merchant_name = Monetbil::getMerchantName();
        $monetbil_merchant_email = Monetbil::getMerchantEmail();
        $monetbil_service_key = Monetbil::getServiceKey();
        $monetbil_service_secret = Monetbil::getServiceSecret();
        $monetbil_service_name = Monetbil::getServiceName();

        $params = array(
            'title' => $title,
            'description' => $description,
            'merchant_name' => $monetbil_merchant_name,
            'merchant_email' => $monetbil_merchant_email,
            'service_key' => $monetbil_service_key,
            'service_secret' => $monetbil_service_secret,
            'service_name' => $monetbil_service_name,
            'version' => $version,
            'url' => Monetbil::getUrl()
        );

        $this->smarty->assign($params);

        return $this->display(__FILE__, $this->getBackTemplate('configuration.tpl'));
    }

    /**
     * @see hookHeader
     */
    public function hookDisplayHeader($params)
    {
        return $this->hookHeader($params);
    }

    /**
     * include js/css file in frontend Header
     */
    public function hookHeader($params)
    {
        if ($this->isPaymentPage()) {
            $this->context->controller->addCSS($this->_path . 'assets/css/style.css', 'all');
        }
    }

    /**
     * @see hookFooter
     */
    public function hookDisplayFooter($params)
    {
        return $this->hookFooter($params);
    }

    /**
     * include js/css file in frontend Footer
     */
    public function hookFooter($params)
    {
        if ($this->isPaymentPage()) {

            $js_path = Monetbil::getServerUrl() . $this->_path . 'assets/js/';

            if (Monetbil::MONETBIL_WIDGET_VERSION_V2 == Monetbil::getWidgetVersion()) {
                $widget = $js_path . 'monetbil.min.js';
            } else {
                $widget = $js_path . 'monetbil-mobile-payments.js';
            }

            return '<script type="text/javascript" src="' . $widget . '"></script>';
        }
    }

    /**
     * @see hookBackOfficeHeader
     */
    public function hookDisplayBackOfficeHeader($params)
    {
        return $this->hookBackOfficeHeader($params);
    }

    /**
     * include css file in backend
     */
    public function hookBackOfficeHeader($params)
    {
        if ($this->isMonetbilConfigPage()) {
            $this->context->controller->addCSS($this->_path . 'assets/admin/css/style.css', 'all');
            $this->context->controller->addCSS($this->_path . 'assets/admin/libs/jquery.niftymodals/css/component.css', 'all');
            $this->context->controller->addJS($this->_path . 'assets/admin/libs/jquery.niftymodals/js/jquery.modalEffects.js', 'all');
        }
    }

    /**
     * @see hookPayment
     */
    public function hookDisplayPayment($params)
    {
        return $this->hookPayment($params);
    }

    /**
     * show module on payment step
     * this hook is used to output the current method of payment to the choice
     * list of available methods on the checkout pages.
     *
     * @return string
     */
    public function hookPayment($params)
    {
        if (!$this->active
                or ! Monetbil::getServiceKey()
                or ! Monetbil::getServiceSecret()
                or ! Monetbil::getWidgetVersion()
        ) {
            return;
        }

        $cart = $this->context->cart;
        $module = $this->module;
        $module instanceof Monetbil;

        if (!$cart instanceof Cart) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        if ($cart->id === null) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $total = Tools::ps_round((float) $this->context->cart->getOrderTotal(true, Cart::BOTH), 0);

        $return_url = $this->context->link->getModuleLink('monetbil', 'payment', array(), true);

        $monetbil_args = array(
            'amount' => $total,
            'user' => $customer->id,
            'currency' => 'XAF',
            'email' => $customer->email,
            'item_ref' => $cart->id,
            'payment_ref' => $cart->secure_key,
            'last_name' => $customer->lastname,
            'first_name' => $customer->firstname,
            'return_url' => $return_url
        );

        $monetbil_args['sign'] = Monetbil::sign(Monetbil::getServiceSecret(), $monetbil_args);

        $this->context->smarty->assign(array(
            'monetbil_args' => $monetbil_args
        ));

        if (Monetbil::MONETBIL_WIDGET_VERSION_V2 == Monetbil::getWidgetVersion()) {
            return $this->display(__FILE__, $this->getHookTemplate('payment_execution_v2.tpl'));
        } else {
            return $this->display(__FILE__, $this->getHookTemplate('payment_execution_v1.tpl'));
        }
    }

    /**
     * hookPaymentReturn
     * this hook is called when a customer has chosen this method of payment
     *
     * @param array $params
     * @return string
     */
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return null;
        }

        $objOrder = null;
        if (array_key_exists('objOrder', $params)) {
            $objOrder = $params['objOrder'];

            if (!Validate::isLoadedObject($objOrder)) {
                Tools::redirect('index.php?controller=order&step=1');
            }
        }

        $total_to_pay = 0;
        if (array_key_exists('total_to_pay', $params)) {
            $total_to_pay = $params['total_to_pay'];
        }

        $this->smarty->assign(array(
            'status' => Monetbil::getQuery('status', 'failed'),
            'valid' => $objOrder->valid,
            'reference' => $objOrder->reference,
            'total_to_pay' => $total_to_pay,
            'this_path' => $this->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
        ));

        return $this->display(__FILE__, $this->getHookTemplate('payment_confirmation.tpl'));
    }

    /**
     * hookDisplayPaymentEU
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayPaymentEU($params)
    {
        
    }

    /**
     * return correct path for .tpl file
     *
     * @param string $file
     * @return string
     */
    public function getHookTemplate($file)
    {
        return '/views/templates/hook/' . $file;
    }

    /**
     * return correct path for .tpl file
     *
     * @param string $file
     * @return string
     */
    public function getFrontTemplate($file)
    {
        return '/views/templates/front/' . $file;
    }

    /**
     * return correct path for .tpl file
     *
     * @param $file
     * @return string
     */
    public function getBackTemplate($file)
    {
        return '/views/templates/back/' . $file;
    }

    /**
     * Check if is payment page
     *
     * @return boolean
     */
    public function isPaymentPage()
    {
        return $this->context->controller instanceof OrderController && $this->context->controller->step == 3;
    }

    /**
     * Check if is config page
     *
     * @return boolean
     */
    public function isMonetbilConfigPage()
    {
        return Monetbil::GATEWAY == Monetbil::getQuery('configure');
    }

    /**
     * getService
     *
     * @param $service_key
     * @param $service_secret
     * @return array
     */
    public static function getService($service_key, $service_secret)
    {
        $postData = array(
            'service_key' => $service_key,
            'service_secret' => $service_secret,
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, Monetbil::GET_SERVICE_URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData, '', '&'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $json = curl_exec($ch);
        $result = json_decode($json, true);

        if (is_array($result)) {
            return $result;
        }

        return array();
    }

    /**
     * sign
     *
     * @return string
     */
    public static function sign($service_secret, $params)
    {
        ksort($params);
        $signature = md5($service_secret . implode('', $params));
        return $signature;
    }

    /**
     * checkSign
     *
     * @return boolean
     */
    public static function checkSign($service_secret, $params)
    {
        if (!array_key_exists('sign', $params)) {
            return false;
        }

        $sign = $params['sign'];
        unset($params['sign']);

        $signature = Monetbil::sign($service_secret, $params);

        return ($sign == $signature);
    }

    /**
     * checkPayment
     *
     * @param string $paymentId
     * @return array ($payment_status, $testmode)
     */
    public static function checkPayment($paymentId)
    {
        $postData = array(
            'paymentId' => $paymentId
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, Monetbil::CHECK_PAYMENT_URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData, '', '&'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $json = curl_exec($ch);
        $result = json_decode($json, true);

        $payment_status = 0;
        $testmode = 0;
        if (is_array($result) and array_key_exists('transaction', $result)) {
            $transaction = $result['transaction'];

            $payment_status = $transaction['status'];
            $testmode = $transaction['testmode'];
        }

        return array($payment_status, $testmode);
    }

    /**
     * getPost
     *
     * @param string $key
     * @param string $default
     * @return string | null
     */
    public static function getPost($key = null, $default = null)
    {
        return $key == null ? $_POST : (isset($_POST[$key]) ? $_POST[$key] : $default);
    }

    /**
     * getQuery
     *
     * @param string $key
     * @param string $default
     * @return string | null
     */
    public static function getQuery($key = null, $default = null)
    {
        return $key == null ? $_GET : (isset($_GET[$key]) ? $_GET[$key] : $default);
    }

    /**
     * getQueryParams
     *
     * @return array
     */
    public static function getQueryParams()
    {
        $queryParams = array();
        $parts = explode('?', Monetbil::getUrl());

        if (isset($parts[1])) {
            parse_str($parts[1], $queryParams);
        }

        return $queryParams;
    }

    /**
     * getServerUrl
     *
     * @return string
     */
    public static function getServerUrl()
    {
        $server_name = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        $scheme = 'http';

        if ('443' === $port) {
            $scheme = 'https';
        }

        $url = $scheme . '://' . $server_name;
        return $url;
    }

    /**
     * getUrl

     * @return string
     */
    public static function getUrl()
    {
        $url = Monetbil::getServerUrl() . Monetbil::getUri();
        return $url;
    }

    /**
     * getUri
     *
     * @return string
     */
    public static function getUri()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $uri = '/' . ltrim($requestUri, '/');

        return $uri;
    }

    /**
     * getServiceKey
     *
     * @return string
     */
    public static function getServiceKey()
    {
        return Configuration::get(Monetbil::MONETBIL_SERVICE_KEY);
    }

    /**
     * getServiceSecret

     * @return string
     */
    public static function getServiceSecret()
    {
        return Configuration::get(Monetbil::MONETBIL_SERVICE_SECRET);
    }

    /**
     * getWidgetVersion
     *
     * @return string
     */
    public static function getWidgetVersion()
    {
        return Configuration::get(Monetbil::MONETBIL_WIDGET_VERSION);
    }

    /**
     * getServiceName
     *
     * @return string
     */
    public static function getServiceName()
    {
        return Configuration::get(Monetbil::MONETBIL_SERVICE_NAME);
    }

    /**
     * getMerchantEmail
     *
     * @return string
     */
    public static function getMerchantEmail()
    {
        return Configuration::get(Monetbil::MONETBIL_MERCHANT_EMAIL);
    }

    /**
     * getMerchantName
     *
     * @return string
     */
    public static function getMerchantName()
    {
        return Configuration::get(Monetbil::MONETBIL_MERCHANT_NAME);
    }

    /**
     * getTitle
     *
     * @return string
     */
    public static function getTitle()
    {
        return Configuration::get(Monetbil::MONETBIL_PAYMENT_TITLE);
    }

    /**
     * getDescription
     *
     * @return string
     */
    public static function getDescription()
    {
        return Configuration::get(Monetbil::MONETBIL_PAYMENT_DESCRIPTION);
    }

    /**
     * getWidgetUrl

     * @return string
     */
    public static function getWidgetUrl()
    {
        $version = Monetbil::getWidgetVersion();
        $service_key = Monetbil::getServiceKey();
        $widget_url = Monetbil::WIDGET_URL . $version . '/' . $service_key;
        return $widget_url;
    }

    /**
     * getWidgetV1Url
     *
     * @param array $monetbil_args
     * @return string
     */
    public static function getWidgetV1Url($monetbil_args)
    {
        $monetbil_v1_redirect = Monetbil::getWidgetUrl() . '?' . http_build_query($monetbil_args, '', '&');
        return $monetbil_v1_redirect;
    }

}
