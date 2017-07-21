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
            {foreach from=$monetbil_args key=key item=value}
                <input type="hidden" name="{$key}" value="{$value}"/>
            {/foreach}
            <button class="btn btn-block btn-primary m-t-20" type="submit" id="monetbil-payment-widget">{l s='Pay by Mobile Money via Monetbil' mod='monetbil'}</button>
        </form>
    </div>
</div>