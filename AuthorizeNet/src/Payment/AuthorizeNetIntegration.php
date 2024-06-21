<?php

namespace Webkul\AuthorizeNet\Payment;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\contract\v1\CustomerDataType;
use net\authorize\api\controller as AnetController;

class AuthorizeNetIntegration
{
    protected const TRANSACTION_TYPE = 'authCaptureTransaction';

    protected string $environment;

    protected AnetAPI\MerchantAuthenticationType $merchantAuthentication;

    /**
     * @param  string  $environment
     * @param  string  $apiLoginId
     * @param  string  $transactionKey
     */
    public function __construct($environment, $apiLoginId, $transactionKey)
    {
        $this->environment = $environment;
        $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $this->merchantAuthentication->setName($apiLoginId);
        $this->merchantAuthentication->setTransactionKey($transactionKey);
    }

    /**
     * Create and process a payment transaction
     *
     * @param  object  $cart
     * @param  string  $dataDescriptor
     * @param  string  $dataValue
     * @return AuthorizeNetIntegrationResponse
     */
    public function createAndAcceptPaymentTransaction($cart, $dataDescriptor, $dataValue)
    {
        $billingAddress = $cart->billing_address;
        $shippingAddress = $cart->shipping_address;

        $opaqueData = $this->createOpaqueData($dataDescriptor, $dataValue);
        $paymentOne = $this->createPaymentType($opaqueData);
        $customerBillingAddress = $this->createCustomerBillingAddressType($billingAddress);
        $customerShippingAddress = $this->createCustomerAddressType($shippingAddress);
        $customerData = $this->createCustomerData($billingAddress, $cart->customer_id);
        $duplicateWindowSetting = $this->createDuplicateWindowSetting();
        $transactionRequestType = $this->createTransactionRequestType(
            $paymentOne,
            $customerBillingAddress,
            $customerShippingAddress,
            $customerData,
            $duplicateWindowSetting,
            $cart->base_grand_total
        );

        // Assemble the complete transaction request. Use the cart id as transaction reference
        $request = $this->createTransactionRequest($transactionRequestType, $cart);

        // Create the controller and get the response
        $controller = $this->createTransactionController($request);

        $response = $controller->executeWithApiResponse($this->environment);

        return new AuthorizeNetIntegrationResponse($response);
    }

    /**
     * @param  string  $dataDescriptor
     * @param  string  $dataValue
     * @return AnetAPI\OpaqueDataType
     */
    protected function createOpaqueData($dataDescriptor, $dataValue)
    {
        $opaqueData = new AnetAPI\OpaqueDataType();
        $opaqueData->setDataDescriptor($dataDescriptor);
        $opaqueData->setDataValue($dataValue);

        return $opaqueData;
    }

    /**
     * @param  AnetAPI\OpaqueDataType  $opaqueData
     * @return AnetAPI\PaymentType
     */
    protected function createPaymentType($opaqueData)
    {
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setOpaqueData($opaqueData);

        return $paymentOne;
    }

    /**
     * @param  object  $customerAddress
     * @return AnetAPI\CustomerAddressType
     */
    protected function createCustomerAddressType($customerAddress)
    {
        $customerAddressType = new AnetAPI\CustomerAddressType();
        $customerAddressType->setFirstName($customerAddress->first_name);
        $customerAddressType->setLastName($customerAddress->last_name);
        $customerAddressType->setAddress($customerAddress->address);
        $customerAddressType->setCity($customerAddress->city);
        $customerAddressType->setState($customerAddress->state);
        $customerAddressType->setZip($customerAddress->postcode);
        $customerAddressType->setCountry($customerAddress->country);
        if (! empty($customerAddress->company_name)) {
            $customerAddressType->setCompany($customerAddress->company_name);
        }

        return $customerAddressType;
    }

    /**
     * @param  object  $billingAddress
     * @return AnetAPI\CustomerAddressType
     */
    protected function createCustomerBillingAddressType($billingAddress)
    {
        // Phone is only available for billing address.
        $customerAddressType = $this->createCustomerAddressType($billingAddress);
        if (! empty($billingAddress->phone)) {
            $customerAddressType->setPhoneNumber($billingAddress->phone);
        }

        return $customerAddressType;
    }

    /**
     * @param  object  $billingAddress
     * @param  object  $customerId
     * @return CustomerDataType
     */
    protected function createCustomerData($billingAddress, $customerId)
    {
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setId($customerId);
        $customerData->setEmail($billingAddress->email);

        return $customerData;
    }

    /**
     * @return AnetAPI\SettingType
     */
    protected function createDuplicateWindowSetting()
    {
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName('duplicateWindow');
        $duplicateWindowSetting->setSettingValue('60');

        return $duplicateWindowSetting;
    }

    /**
     * @return AnetAPI\TransactionRequestType
     */
    protected function createTransactionRequestType($paymentOne, $customerBillingAddress, $customerShippingAddress, $customerData, $duplicateWindowSetting, $amount)
    {
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType(self::TRANSACTION_TYPE);
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerBillingAddress);
        $transactionRequestType->setShipTo($customerShippingAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

        return $transactionRequestType;
    }

    /**
     * @return AnetAPI\CreateTransactionRequest
     */
    protected function createTransactionRequest($transactionRequestType, $cart)
    {
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setRefId($cart->id);
        $request->setTransactionRequest($transactionRequestType);

        return $request;
    }

    /**
     * @return AnetController\CreateTransactionController
     */
    protected function createTransactionController($request)
    {
        return new AnetController\CreateTransactionController($request);
    }
}
