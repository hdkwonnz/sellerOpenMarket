<?php

namespace App\Http\Controllers\Seller;

use App\Order;
use Illuminate\Http\Request;
use App\Jobs\OrderdetailsEmailJob;
use App\Http\Controllers\Controller;

class SellerController extends Controller
{
    public function index()
    {
        return view('seller/seller/index');
    }

    public function editOrder(Request $request)
    {
        $request->validate([
            'orderId' => 'required|numeric',
        ]);

        //do update....

        return response()->json([
            'successMsg' => 'good',
        ]);
    }

    public function reSendMailForOrder(Request $request)
    {
        //return $request->all();

        $orderDetails = Order::with('user','address','orderdetails')
                        ->where('id','=',$request->orderId)
                        ->first();

        ////email to customer
        ////보낼 메일을 jobs table로 passing 한다.
        ////반드시 php artisan queue:work --tries=2 --sleep=10 type 할 것.
        $job = (new OrderdetailsEmailJob($orderDetails))
                ->delay(now()->addSeconds(10));
        $this->dispatch($job);

        return response()->json([
            'successMsg' => "Completed to send email to buyer."
        ]);
    }

    public function customerOrdersByTerm()
    {
        // $fromDate = date('Y-m-d H:i:s',strtotime(request('fromDate')));
        // $toDate = date('Y-m-d H:i:s',strtotime(request('toDate')));

        //// https://stackoverflow.com/questions/43871752/how-to-get-created-at-date-only-not-time-in-laravel-5-4
        $orders = Order::with('user','orderdetails','address')
                        ->whereDate('created_at', '>=', request('fromDate'))
                        ->whereDate('created_at', '<=', request('toDate'))
                        ->orderBy('created_at','desc')
                        ->get();

        // return $orders;

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function customerOrderById($id)
    {
        if ($id == "" || null){
            $errorMsg = "Please enter order Id.";

            return view('order.orderDetailsById', compact('errorMsg'));
        }

        $order = Order::with('user','orderdetails','address')
                        ->where('id','=',$id)
                        ->first();

        // return $orders;

        if (!$order){
            $errorMsg = "Order does not exist.";
        }else{
            $errorMsg = null;
        }

        return view('seller.seller.showCustomerOrderById', compact('order','errorMsg'));
        // $orderDetails = $order;
        // return view('emails.toCustomer.orderDetails', compact('orderDetails','errorMsg'));
    }


    public function customerOrders()
    {
        $todayDate = date("Y-m-d");

        $orders = Order::with('user','orderdetails','address')
                        ->whereDate('created_at','=',$todayDate)
                        ->get();

        return response()->json([
                            'orders' => $orders,
        ]);
    }

    public function showCustomerOrders()
    {
        return view('seller.seller.showCustomerOrders');
    }
}
