# Bagisto Authorize.Net Payment Gateway

This package integrates Authorize.Net payment gateway with Bagisto e-commerce platform, providing seamless payment processing for your store.

##  Requirements

   The package has been development using Bagisto `2.1.2`, but should work with any `2.x.x` version.

## Features

- Authorize.Net payment integration
- Support for authorization and capture transactions
- Customer billing and shipping address handling
- Configurable sandbox and live environments
- Error handling and response processing

## Installation

1. **Clone the repository**

   ```sh
   git clone https://github.com/diegosenarruzza/authorize-net-bagisto-package.git
   ```


2. **Copy package into your bagisto packages directory**

   ```sh
   cp -r ./authorize-net-bagisto-package/AuthorizeNet  <your-proyect-root>/packages/Webkul/AuthorizeNet
   ```


3. **Configure service providers**

   - Go to **config/app.php** file and add the following line under **'providers'** array.
      ```php
      Webkul\AuthorizeNet\Providers\AuthorizeNetServiceProvider::class,
      ```

   - Go to **config/concord.php** file and add the following line under **'modules'** array.
      ```php
      \Webkul\AuthorizeNet\Providers\ModuleServiceProvider::class,
      ```

4. **Configure psr-4 package**
   - Go to **composer.json** file and add the following line under **'autoload' > psr-4'**
   ```json
   "Webkul\\AuthorizeNet\\": "packages/Webkul/AuthorizeNet/src"
   ```

5. **Add the vue.js template into checkout page**

   _Because there is no ideal event-hook to subscribe the vue.js template to at the moment, it is easier to include it by hand within the checkout flow (analogous to how the paypal one that comes by default is currently)._

   - Go to *paclages/Webkul/Shop/src/Resources/views/checkout/onepage/index.blade.php*
   - Find the section with comment `<!-- Included Checkout Summary Blade File -->`
   - Under the `v-if="canPlaceOrder"` put the next template
   ```html
   <template v-if="cart.payment_method == 'authorizenet'">
      <v-auhtorizenet-smart-button></v-auhtorizenet-smart-button>
   </template>
   ```
   You should see something like next
   ```html
     <!-- Included Checkout Summary Blade File -->
     <div class="sticky top-8 h-max w-[442px] max-w-full ltr:pl-8 rtl:pr-8 max-lg:w-auto max-lg:max-w-[442px] max-lg:ltr:pl-0 max-lg:rtl:pr-0">
         @include('shop::checkout.onepage.summary')

         <div
             class="flex justify-end"
             v-if="canPlaceOrder"
         >
            <template v-if="cart.payment_method == 'paypal_smart_button'">
               {!! view_render_event('bagisto.shop.checkout.onepage.summary.paypal_smart_button.before') !!}

               <v-paypal-smart-button></v-paypal-smart-button>

               {!! view_render_event('bagisto.shop.checkout.onepage.summary.paypal_smart_button.after') !!}
            </template>

            <template v-if="cart.payment_method == 'authorizenet'">
               <v-auhtorizenet-smart-button></v-auhtorizenet-smart-button>
            </template>

         <template v-else>
            <x-shop::button
               type="button"
               class="primary-button w-max py-3 px-11 bg-navyBlue rounded-2xl max-sm:text-sm max-sm:px-6 max-sm:mb-10"
               :title="trans('shop::app.checkout.onepage.summary.place-order')"
               ::disabled="isPlacingOrder"
               ::loading="isPlacingOrder"
               @click="placeOrder"
            />
          </template>
      </div>
   ```

6. Run the next commands to complete the setup
   ```sh
   composer dump-autoload
   composer require authorizenet/authorizenet
   php artisan route:cache
   php artisan vendor:publish
   ```
   With `php artisan vendor:publish`, select the option to publish all providers and tags.


## Configure the Payment Method

   - Open your application and
      - go to [127.0.0.1:8000/admin/configuration/sales/payment_methods](http://127.0.0.1:8000/admin/configuration/sales/payment_methods), or
      - Login in Admin and navigate to *Configure >> Sales >> Payment Methods*

   - Upload a logo to show to clients when are in payment methods select (is not required, but there is no default yet).
   - Set the keys obtained in authorize.net: *Client Key*, *API Login ID*, *Transaction Key*.
   - Select the *status* to enable the payment method.
   - Enable if is Sandbox mode (development/test/qa) or disable if you are in production.

## Tests
   _Pending_

## Contributing
Bug reports and pull requests are welcome on GitHub at [https://github.com/diegosenarruzza/authorizenet-bagisto-package](https://github.com/diegosenarruzza/authorizenet-bagisto-package).

## License
The package is available as open source under the terms of the [Mit License](https://opensource.org/license/mit/).