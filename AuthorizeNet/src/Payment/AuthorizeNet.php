<?php

namespace Webkul\AuthorizeNet\Payment;

use Webkul\Payment\Payment\Payment;
use Illuminate\Support\Facades\Storage;

class AuthorizeNet extends Payment
{

    protected $code = 'authorizenet';

    public function getImage()
    {
        $url = $this->getConfigData('image');

        return Storage::url($url);
    }

    public function getRedirectUrl()
    {
        return route('authorizenet.process.order');
    }

}
