<?php

namespace Webkul\AuthorizeNet\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\AuthorizeNet\Payment\AuthorizeNetPayment;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Shop\Http\Controllers\API\OnepageController;

class AuthorizeNetController extends Controller
{
    public function __construct(
        protected AuthorizeNetPayment $authorizeNetPayment,
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected CustomerRepository $customerRepository
    ) {
    }

    public function processOrder()
    {
        try {
            $this->validateOrder();

            $this->authorizeNetPayment->processOrder(request()->input('response'));

            return $this->saveOrder();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    protected function saveOrder()
    {
        try {
            $order = $this->orderRepository->create(Cart::prepareDataForOrder());

            $this->orderRepository->update(['status' => 'processing'], $order->id);

            Cart::deActivateCart();

            session()->flash('order', $order);

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', trans('shop::app.common.error'));

            throw $e;
        }
    }

    protected function validateOrder()
    {
        (new OnepageController($this->orderRepository, $this->customerRepository))->validateOrder();
    }
}
