<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    // Get Fuel Orders Assigned to Driver
    public function getFuelOrders(Request $request)
    {
        $orders = Order::get()->where('driverID', $request->input('driverID'));
        return response()->json([
            'orders' => $orders
        ], 200);
    }

    // Accept Fuel Order
    public function acceptFuelOrder(Request $request)
    {
        $order = Order::find($request->input('orderID'));
        $order->status = 'Delivering';
        $order->save();

        return response()->json([
            'message' => 'Fuel order accepted successfully',
            'order' => $order
        ], 200);
    }

    // Confirm Completion of Fuel Delivery
    public function confirmFuelDelivery(Request $request)
    {
        $order = Order::find($request->input('orderID'));
        $order->status = 'delivered';
        $order->save();

        return response()->json([
            'message' => 'Fuel delivery confirmed successfully',
            'order' => $order
        ], 200);
    }
}
