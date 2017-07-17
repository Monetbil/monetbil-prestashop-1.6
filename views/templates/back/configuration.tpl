help{*
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

<fieldset>
    <legend>
        <img src="../modules/monetbil/assets/img/monetbil.png" alt="Monetbil" style="margin-left: -9px;">
    </legend>
    {if $merchant_name }
        <p class="alert alert-info">
            <i class="icon-check"></i> {l s='Service perfectly configured' mod='monetbil'}
        </p>
    {/if}
    <div class="container">
        {if $merchant_name }
        {else}
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-9">
                            <div style="text-align:left;margin-bottom:30px;">
                                <a class="btn-green" href="https://www.monetbil.com/try-monetbil?partner_url={$url|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Create an account' mod='monetbil'}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        <div class="row">
            <div class="col-md-6">
                <form method="post" action="{$url|escape:'htmlall':'UTF-8'}">
                    {if $merchant_name }
                    {else}
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <p>{l s='What you should do:' mod='monetbil'}</p>
                                <ul>
                                    <li>{l s='Sign into your account' mod='monetbil'} <a class="monetbil_link" href="https://www.monetbil.com/login" target="_blank">Monetbil</a>;</li>
                                    <li>{l s='Select' mod='monetbil'} <a class="monetbil_link" href="https://www.monetbil.com/services" target="_blank">{l s='service' mod='monetbil'}</a> {l s='for your shop;' mod='monetbil'}</li>
                                    <li>{l s='Copy and paste the parameters below.' mod='monetbil'}</li>
                                </ul>
                            </div>
                        </div>
                    {/if}
                    <div class="row">
                        <div class="col-md-3 no-padding">
                            <label for="{Monetbil::MONETBIL_PAYMENT_TITLE}"><strong>{l s='Title' mod='monetbil'}</strong></label>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <input type="text" class="full-width form-control form-control2 required" required="" value="{$title|escape:'htmlall':'UTF-8'}" id="{Monetbil::MONETBIL_PAYMENT_TITLE}" name="{Monetbil::MONETBIL_PAYMENT_TITLE}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 no-padding">
                            <label for="{Monetbil::MONETBIL_PAYMENT_DESCRIPTION}"><strong>{l s='Description' mod='monetbil'}</strong></label>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <textarea class="full-width form-control form-control2 required" required="" id="{Monetbil::MONETBIL_PAYMENT_DESCRIPTION}" name="{Monetbil::MONETBIL_PAYMENT_DESCRIPTION}">{$description|escape:'htmlall':'UTF-8'}</textarea>
                            </div>
                        </div>
                    </div>
                    {if $merchant_name }
                        <div class="row">
                            <div class="col-md-3 no-padding">
                                <label for="{Monetbil::MONETBIL_MERCHANT_NAME}"><strong>{l s='Merchant name' mod='monetbil'}</strong></label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <input type="text" class="full-width form-control form-control2" disabled="" value="{$merchant_name|escape:'htmlall':'UTF-8'}" id="{Monetbil::MONETBIL_MERCHANT_NAME}" name="{Monetbil::MONETBIL_MERCHANT_NAME}">
                                </div>
                            </div>
                        </div>
                    {/if}
                    {if $merchant_email }
                        <div class="row">
                            <div class="col-md-3 no-padding">
                                <label for="{Monetbil::MONETBIL_MERCHANT_EMAIL}"><strong>{l s='Merchant email' mod='monetbil'}</strong></label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <input type="text" class="full-width form-control form-control2" disabled="" value="{$merchant_email|escape:'htmlall':'UTF-8'}" id="{Monetbil::MONETBIL_MERCHANT_EMAIL}" name="{Monetbil::MONETBIL_MERCHANT_EMAIL}">
                                </div>
                            </div>
                        </div>
                    {/if}
                    {if $service_name }
                        <div class="row">
                            <div class="col-md-3 no-padding">
                                <label for="{Monetbil::MONETBIL_SERVICE_NAME}"><strong>{l s='Service name' mod='monetbil'}</strong></label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <input type="text" class="full-width form-control form-control2" disabled="" value="{$service_name|escape:'htmlall':'UTF-8'}" id="{Monetbil::MONETBIL_SERVICE_NAME}" name="{Monetbil::MONETBIL_SERVICE_NAME}">
                                </div>
                            </div>
                        </div>
                    {/if}
                    <div class="row">
                        <div class="col-md-3 no-padding">
                            <label for="{Monetbil::MONETBIL_SERVICE_KEY}"><strong>{l s='Service key' mod='monetbil'}</strong></label>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <input type="text" class="full-width form-control form-control2 required" required="" value="{$service_key|escape:'htmlall':'UTF-8'}" id="{Monetbil::MONETBIL_SERVICE_KEY}" name="{Monetbil::MONETBIL_SERVICE_KEY}">
                            </div>
                            <a href="javascript:;" class="monetbil_service_help md-trigger" data-modal="service-key-modal">
                                <img src="../modules/monetbil/assets/img/help_symbol.png">
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 no-padding">
                            <label for="{Monetbil::MONETBIL_SERVICE_SECRET}"><strong>{l s='Service secret' mod='monetbil'}</strong></label>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <input type="text" class="full-width form-control form-control2 required" required="" value="{$service_secret|escape:'htmlall':'UTF-8'}" id="{Monetbil::MONETBIL_SERVICE_SECRET}" name="{Monetbil::MONETBIL_SERVICE_SECRET}">
                            </div>
                            <a href="javascript:;" class="monetbil_service_help md-trigger" data-modal="service-secret-modal">
                                <img src="../modules/monetbil/assets/img/help_symbol.png">
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 no-padding">
                            <label for="{Monetbil::MONETBIL_WIDGET_VERSION}"><strong>{l s='Select version' mod='monetbil'}</strong></label>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <select name="{Monetbil::MONETBIL_WIDGET_VERSION}" id="{Monetbil::MONETBIL_WIDGET_VERSION}" class="full-width form-control form-control2 required">
                                    <option value="v2"{if $version == 'v2' } selected=""{/if}>{l s='Version 2 (Responsive)' mod='monetbil'}</option>
                                    <option value="v1"{if $version == 'v1' } selected=""{/if}>{l s='Version 1 (Not responsive)' mod='monetbil'}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <div class="form-group">
                                <input type="submit" class="sf-button large lightblue outerglow full-width" value="{l s='Save Changes' mod='monetbil'}" name="MonetbilServcieConfig">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</fieldset>
<div id="service-key-modal" class="md-modal colored-header custom-width md-effect-9" style="width: 100%;">
    <div class="md-content">
        <div class="modal-header">
            <h3>{l s='Service key' mod='monetbil'}</h3>
        </div>
        <div class="modal-body">
            <img alt="" src="../modules/monetbil/assets/img/service_key.png">
        </div>
        <div class="modal-footer">
            <button class="md-close" data-dismiss="modal" type="button">{l s='Close' mod='monetbil'}</button>
        </div>
    </div>
</div>
<div id="service-secret-modal" class="md-modal colored-header custom-width md-effect-9" style="width: 100%;">
    <div class="md-content">
        <div class="modal-header">
            <h3>{l s='Service key' mod='monetbil'}</h3>
        </div>
        <div class="modal-body">
            <img alt="" src="../modules/monetbil/assets/img/service_secret.png">
        </div>
        <div class="modal-footer">
            <button class="md-close" data-dismiss="modal" type="button">{l s='Close' mod='monetbil'}</button>
        </div>
    </div>
</div>
<div class="md-overlay"></div>