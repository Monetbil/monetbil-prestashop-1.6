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

<div class="box-monetbil">
    {if $status == 'success'}
        {if $valid == 1}
            <h3>{l s='Successful payment' mod='monetbil'}</h3>
        {else}
            <h3>{l s='Successful payment' mod='monetbil'} - TESTMODE</h3>
        {/if}
    {elseif $status == 'cancelled'}
        <h3>{l s='Transaction cancelled' mod='monetbil'}</h3>
    {else}
        <h3>{l s='Payment failed' mod='monetbil'}</h3>
    {/if}
    <p><strong class="dark">{l s='Order reference' mod='monetbil'}</strong> {$reference|escape:'html':'UTF-8'}</p>
    <p><strong class="dark">{l s='Payment method' mod='monetbil'}</strong> Monetbil</p>
</div>