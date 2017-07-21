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
 * Class MonetbilreturnModuleFrontController
 *
 * process action with module on payment method page
 */
class MonetbilreturnModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {

        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php');
        }

        $params = Monetbil::getQueryParams();
        $service_secret = Monetbil::getServiceSecret();

        if (!Monetbil::checkSign($service_secret, $params)) {
            Tools::redirect('index.php');
        }

        $user = (int) Monetbil::getQuery('user');

        $customer = new Customer($user);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $module = $this->module;
        $module instanceof Monetbil;

        $item_ref = Monetbil::getQuery('item_ref');

        $cart_id = (int) $item_ref;
        $cart = new Cart($cart_id);

        if (!$cart instanceof Cart) {
            Tools::redirect('index.php');
        }

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

        $id_order = Order::getOrderByCartId($cart_id);

        if ($id_order === false) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $status = Monetbil::getQuery('status');

        Tools::redirect('index.php?controller=order-confirmation' . '&' . http_build_query(array(
                    'status' => $status,
                    'id_cart' => $cart->id,
                    'id_module' => $module->id,
                    'id_order' => $id_order,
                    'key' => $cart->secure_key
        )));
    }

}
