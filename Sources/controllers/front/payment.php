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

/**
 * Class MonetbilpaymentModuleFrontController
 *
 * process action with module on payment method page
 */
class MonetbilpaymentModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php');
        }

        $module = $this->module;
        $module instanceof Monetbil;

        $item_ref = Monetbil::getQuery('item_ref');
        $transaction_id = Monetbil::getQuery('transaction_id');
        $user = (int) Monetbil::getQuery('user');

        $cart_id = (int) $item_ref;
        $cart = new Cart($cart_id);

        if (!$cart instanceof Cart) {
            Tools::redirect('index.php');
        }

        $currency = new Currency($cart->id_currency);

        $total = Tools::ps_round((float) $cart->getOrderTotal(true, Cart::BOTH), 0);

        if (!$module->active
                or $cart->id_customer == 0
                or $cart->id_address_delivery == 0
                or $cart->id_address_invoice == 0
        ) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available
        $authorized = false;
        foreach (Module::getPaymentModules() as $paymentModule) {
            if ($paymentModule['name'] == $module->name) {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $params = Monetbil::getQueryParams();
        if (!Monetbil::checkSign(Monetbil::getServiceSecret(), $params)) {
            Tools::redirect('index.php');
        }

        list($payment_status, $testmode) = Monetbil::checkPayment($transaction_id);

        $order_state = 0;
        $status = 'failed';
        if (Monetbil::STATUS_SUCCESS == $payment_status
                or Monetbil::STATUS_SUCCESS_TESTMODE == $payment_status
        ) {
            // Payment has been successful
            $order_state = Configuration::get(Monetbil::MONETBIL_OS_SUCCESS_PAYMENT);
            $status = 'success';

            if ($testmode) {
                $order_state = Configuration::get(Monetbil::MONETBIL_OS_SUCCESS_PAYMENT_TESTMODE);
            }
        } elseif (Monetbil::STATUS_CANCELLED == $payment_status
                or Monetbil::STATUS_CANCELLED_TESTMODE == $payment_status) {

            // Payment cancelled
            $order_state = Configuration::get(Monetbil::MONETBIL_OS_CANCELLED_PAYMENT);
            $status = 'cancelled';

            if ($testmode) {
                $order_state = Configuration::get(Monetbil::MONETBIL_OS_CANCELLED_PAYMENT_TESTMODE);
            }
        } elseif (Monetbil::STATUS_FAILED == $payment_status
                or Monetbil::STATUS_FAILED_TESTMODE == $payment_status) {

            // Payment failed
            $order_state = Configuration::get(Monetbil::MONETBIL_OS_FAILED_PAYMENT);
            $status = 'failed';

            if ($testmode) {
                $order_state = Configuration::get(Monetbil::MONETBIL_OS_FAILED_PAYMENT_TESTMODE);
            }
        }

        $customer = new Customer($user);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        try {
            $module->validateOrder($cart_id, $order_state, (float) $total, $module->displayName, null, array(), (int) $currency->id, false, $cart->secure_key);
        } catch (Exception $exc) {
            ($exc);
            Tools::redirect('index.php?controller=order&step=1');
        }

        $id_order = Order::getOrderByCartId($cart_id);

        if ($id_order === false) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        Tools::redirect('index.php?controller=order-confirmation' . '&' . http_build_query(array(
                    'status' => $status,
                    'id_cart' => $cart->id,
                    'id_module' => $module->id,
                    'id_order' => $id_order,
                    'key' => $cart->secure_key
        )));
    }

}
