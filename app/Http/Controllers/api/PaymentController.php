<?php

namespace App\Http\Controllers\api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\ShippingAddress;
use Session;
use PayPal\Api\PaymentExecution;
use App\Carts;
/**
 * Class PaymentController
 * @package App\Http\Controllers\api
 */
class PaymentController extends Controller
{
    private $apiContext;
    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('paypal.client_id'),
                config('paypal.secret')
            )
        );
        $this->apiContext->setConfig(config('paypal.settings'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $payment_id = Session::get('payment_id');
        Session::forget('payment_id');
        $execution = new PaymentExecution();
        $execution->setPayerId($request->input('PayerID'));
        $payment = Payment::get($payment_id, $this->apiContext);
        try {
            $result = $payment->execute($execution, $this->apiContext);
            if ($result->getState() == 'approved') {
                return response()->json(['status' => true]);
            }
            return response()->json(['status' => false]);
        } catch (Exception $e) {
            return response()->json(['status' => false]);
        }
    }
    /**
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $userId)
    {
        if (Carts::where('carts_user_id_foreign', $userId)->exists()) {
            $subTotalPrice = 0;
            $cart_list = Carts::where('carts_user_id_foreign', $userId)->get();
            $payer = new Payer();
            $payer->setPaymentMethod("paypal");
            $array_item = [];
            foreach ($cart_list as $item) {
                $subTotalPrice += $item['price'];
                $item_temp = new Item();
                $item_temp->setName($item['product_name'])
                    ->setCurrency('VND')
                    ->setQuantity($item['quantity'])
                    ->setPrice($item['price']);
                $array_item[] = $item_temp;
            }
            $shipping = new ShippingAddress();
            $shipping->setLine1('123 1st St.')
                ->setCity('Ho Chi Minh')
                ->setState('CA')
                ->setPostalCode("70000")
                ->setCountryCode("VN")
                ->setPhone("84930000000");
            $itemList = new ItemList();
            $itemList->setItems($array_item)
                     ->setShippingAddress($shipping);
            $details = new Details();
            $details->setShipping(0)
                ->setTax(0)
                ->setSubtotal($subTotalPrice);
            $amount = new Amount();
            $amount->setCurrency("VND")
                ->setTotal($subTotalPrice + $details->getShipping() + $details->getTax())
                ->setDetails($details);
            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($itemList)
                ->setDescription("Payment description")
                ->setInvoiceNumber(uniqid());
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(route('payment.create'))
                ->setCancelUrl(route('payment.create'));
            $payment = new Payment();
            $payment->setIntent("sale")
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions(array($transaction));
            try {
                $payment->create($this->apiContext);
            } catch (\PayPal\Exception\PPConnectionException $paypalException) {
                throw new \Exception($paypalException->getMessage());
            }
            $approvalUrl = $payment->getApprovalLink();
            Session::put('payment_id', $payment->id);
            return redirect()->to($approvalUrl);
        }
        else {
            return response()->json(['status' => false]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}