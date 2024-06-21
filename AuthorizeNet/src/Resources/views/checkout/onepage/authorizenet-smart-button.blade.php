@if (
    request()->routeIs('shop.checkout.onepage.index')
    && core()->getConfigData('sales.payment_methods.authorizenet.active')
)
    @php
        $isSandbox = core()->getConfigData('sales.payment_methods.authorizenet.sandbox');
        $clientKey = core()->getConfigData('sales.payment_methods.authorizenet.client_key');
        $apiLoginId = core()->getConfigData('sales.payment_methods.authorizenet.api_login_id');
    @endphp

<form method="POST" action="">
    <input type="hidden" name="dataValue" id="dataValue"/>
    <input type="hidden" name="dataDescriptor" id="dataDescriptor"/>
    <button
        type="button"
        id="authorization-net-pay-button"
        style="display: none;"
        class="AcceptUI"
        data-billingAddressOptions='{"show":false, "required":false}'
        data-apiLoginID="{{$apiLoginId}}"
        data-clientKey="{{$clientKey}}"
        data-acceptUIFormBtnTxt="Submit"
        data-acceptUIFormHeaderTxt="Card Information"
        data-responseHandler="responseHandler"
    ></button>
</form>

    @pushOnce('scripts')
        @if ($isSandbox)
            <script type="text/javascript"  src="https://jstest.authorize.net/v3/AcceptUI.js" charset="utf-8">
            </script>
        @else
            <script type="text/javascript" src="https://js.authorize.net/v3/AcceptUI.js" charset="utf-8">
            </script>
        @endif

        <script type="text/x-template" id="v-authorizenet-smart-button-template">
            <x-shop::button
                type="button"
                class="primary-button w-max py-3 px-11 bg-navyBlue rounded-2xl max-sm:text-sm max-sm:px-6 max-sm:mb-10"
                :title="trans('shop::app.checkout.onepage.summary.place-order')"
                ::loading="disabled"
                ::disabled="disabled"
                @click="openAuthorizeNetModal"
            />
        </script>

        <script type="module">
            app.component('v-auhtorizenet-smart-button', {
                template: '#v-authorizenet-smart-button-template',

                data() {
                    return {
                        isSandbox: false,
                        disabled: false
                    }
                },

                mounted() {
                    window.responseHandler = this.responseHandler;
                },

                created() {
                    this.isSandbox = '@json($isSandbox)';
                },

                methods: {
                    openAuthorizeNetModal() {
                        document.getElementById('authorization-net-pay-button').click();
                    },

                    alertBox(message) {
                        this.$emitter.emit('add-flash', { type: 'error', message: message });
                    },

                    isResponseError(response) {
                        return response.messages.resultCode === 'Error'
                    },

                    buildErrorMessage(response) {
                        const messages = response.messages.map(message => `${message.code}: ${message.text}`);
                        return messages.join('\n');
                    },

                    responseHandler(response) {
                        if (this.isSandbox) {
                            console.log(response);
                        }
                        if (this.isResponseError(response)) {
                            this.alertBox(
                                this.buildErrorMessage(response)
                            );
                        } else {
                            this.paymentFormUpdate(response);
                        }
                    },

                    paymentFormUpdate(response) {
                        this.disabled = true;

                        this.$axios.post("{{ route('authorizenet.process.order') }}", {
                            _token: "{{ csrf_token() }}",
                            response: response
                        })
                        .then(response => {
                            if (response.data.success) {
                                window.location.href = "{{ route('shop.checkout.onepage.success') }}";
                            }
                        })
                        .catch(error => {
                            this.alertBox(error.response.data.message)
                        })
                        .finally(() => {
                            this.disabled = false;
                        });
                    }
                }
            })
        </script>
    @endpushonce
@endif
