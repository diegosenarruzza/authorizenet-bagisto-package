<?php

namespace Webkul\AuthorizeNet\Payment;

class AuthorizeNetIntegrationResponse
{
    protected const OK_RESULT_CODE = 'Ok';

    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * Check if the transaction was successful.
     *
     * @return bool
     */
    public function success()
    {
        if ($this->response != null && $this->response->getMessages()->getResultCode() == self::OK_RESULT_CODE) {
            // Check if the API request was successfully received and acted upon
            $transactionResponse = $this->response->getTransactionResponse();

            // Check if there is a transaction response and if it has messages
            return $transactionResponse != null && $transactionResponse->getMessages() != null;
        }

        return false;
    }

    /**
     * Get the error message if the transaction failed.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $errorMessage = '';

        if ($this->response != null) {
            $transactionResponse = $this->response->getTransactionResponse();

            if ($this->response->getMessages()->getResultCode() == self::OK_RESULT_CODE) {

                if (! ($transactionResponse != null && $transactionResponse->getMessages() != null)) {
                    $errorMessage = 'Error Code  : '.$transactionResponse->getErrors()[0]->getErrorCode();
                    $errorMessage .= "\nError Message : ".$transactionResponse->getErrors()[0]->getErrorText();
                }
            } else {
                if ($transactionResponse != null && $transactionResponse->getErrors() != null) {
                    $errorMessage = 'Error Code  : '.$transactionResponse->getErrors()[0]->getErrorCode();
                    $errorMessage .= "\nError Message : ".$transactionResponse->getErrors()[0]->getErrorText();
                } else {
                    $errorMessage = 'Error Code  : '.$this->response->getMessages()->getMessage()[0]->getCode();
                    $errorMessage .= "\nError Message : ".$this->response->getMessages()->getMessage()[0]->getText();
                }
            }
        } else {
            $errorMessage = 'Unexpected error. No response returned.';
        }

        return $errorMessage;
    }
}
