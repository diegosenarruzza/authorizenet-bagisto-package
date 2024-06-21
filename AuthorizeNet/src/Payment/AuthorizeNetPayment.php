<?php

namespace Webkul\AuthorizeNet\Payment;

use Exception;
use net\authorize\api\constants\ANetEnvironment;
use Webkul\Checkout\Facades\Cart;

class AuthorizeNetPayment extends AuthorizeNet
{
    /**
     * @throws Exception
     */
    public function processOrder($authorizeNetResponse)
    {
        $cart = Cart::getCart();

        $dataValue = $authorizeNetResponse['opaqueData']['dataValue'];
        $dataDescriptor = $authorizeNetResponse['opaqueData']['dataDescriptor'];

        $integrationResponse = $this->integration()->createAndAcceptPaymentTransaction($cart, $dataDescriptor, $dataValue);

        if (! $integrationResponse->success()) {
            throw new Exception($integrationResponse->getErrorMessage());
        }
    }

    protected function integration()
    {
        return new AuthorizeNetIntegration(
            $this->environment(),
            $this->apiLoginId(),
            $this->transactionKey()
        );
    }

    protected function environment()
    {
        $isSandbox = $this->getConfigData('sandbox') ?: false;

        return $isSandbox ? ANetEnvironment::SANDBOX : ANetEnvironment::PRODUCTION;
    }

    protected function apiLoginId()
    {
        return $this->getConfigData('api_login_id');
    }

    protected function transactionKey()
    {
        return $this->getConfigData('transaction_key');
    }
}
