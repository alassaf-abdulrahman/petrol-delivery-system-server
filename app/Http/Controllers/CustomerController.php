<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class CustomerController extends Controller
{
    // Place Fuel Order
    public function PlaceFuelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuelType' => 'required|string|in:petrol,diesel,gas',
            'quantity' => 'required|numeric|min:1',
            'deliveryLocation' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the fuel order
            $fuelOrder = Order::create([
                'customerID' => $request->input('customerID'),
                'fuelType' => $request->input('fuelType'),
                'quantity' => $request->input('quantity'),
                'deliveryLocation' => $request->input('deliveryLocation'),
                'status' => 'pending',
                'orderTime' => now()->format('H:i:s'),
                'orderDate' => now()->format('Y-m-d')
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Fuel order placed successfully',
                'fuelOrder' => $fuelOrder
            ], 201);
        } catch (\Exception $e) {
            // An error occurred; rollback the transaction
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to place fuel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Track Fuel Order
    public function TrackFuelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderID' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Retrieve the fuel order
        $fuelOrder = Order::find($request->input('orderID'));

        if (!$fuelOrder) {
            return response()->json([
                'message' => 'Fuel order not found'
            ], 404);
        }

        return response()->json([
            'fuelOrder' => $fuelOrder
        ], 200);
    }

    // Make Payment
    public function MakePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderID' => 'required|numeric',
            'amount' => 'required|numeric|min:1',
            'paymentMethod' => 'required|string|in:credit_card,paypal',
            'paymentReference' => 'required|string|max:255',
            'cardNumber' => 'required|string|size:20',
            'expiryDate' => 'required|string|size:20',
            'cvv' => 'required|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Retrieve the fuel order
            $fuelOrder = Order::find($request->input('orderID'));

            if (!$fuelOrder) {
                return response()->json([
                    'message' => 'Fuel order not found'
                ], 404);
            }

            $payment = Payment::create([
                'orderID' => $request->input('orderID'),
                'amount' => $request->input('amount'),
                'paymentMethod' => $request->input('paymentMethod'),
                'status' => 'paid',
                'cardNumber' => $request->input('cardNumber'),
                'expiryDate' => $request->input('expiryDate'),
                'cvv' => $request->input('cvv'),
                'address' => $request->input('address'),
                'zipCode' => $request->input('zipCode')
            ]);

            // Update the fuel order
            // $fuelOrder->status = 'paid';
            $fuelOrder->amount = $request->input('amount');
            $fuelOrder->save();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Payment made successfully',
            ], 200);
        } catch (\Exception $e) {
            // An error occurred; rollback the transaction
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to make payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
