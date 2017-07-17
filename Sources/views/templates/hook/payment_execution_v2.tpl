{*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License or any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*}
<div class="row">
    <div class="col-xs-12">
        <form action="{Monetbil::getWidgetUrl()}" method="post" data-monetbil="form">
            <input type="hidden" name="amount" value="{$monetbil_args['amount']}"/>
            <input type="hidden" name="currency" value="{$monetbil_args['currency']}"/>
            <input type="hidden" name="item_ref" value="{$monetbil_args['item_ref']}"/>
            <input type="hidden" name="payment_ref" value="{$monetbil_args['payment_ref']}"/>
            <input type="hidden" name="user" value="{$monetbil_args['user']}"/>
            <input type="hidden" name="first_name" value="{$monetbil_args['first_name']}"/>
            <input type="hidden" name="last_name" value="{$monetbil_args['last_name']}"/>
            <input type="hidden" name="email" value="{$monetbil_args['email']}"/>
            <input type="hidden" name="return_url" value="{$monetbil_args['return_url']}"/>
            <button class="btn btn-block btn-primary m-t-20" type="submit" id="monetbil-payment-widget">{l s='Pay by Mobile Money via Monetbil' mod='monetbil'}</button>
        </form>
    </div>
</div>