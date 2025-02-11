<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class CustomerController extends Controller
{
    // Place Fuel Order
    public function placeFuelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuelType' => 'required|string|in:petrol,diesel,gas',
            'quantity' => 'required|numeric|min:1',
            'deliveryLocation' => 'required|string|max:500',
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
    public function trackFuelOrder(Request $request)
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
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderID' => 'required|numeric',
            'amount' => 'required|numeric|min:1',
            'paymentMethod' => 'required|string|in:visa,paypal',
            'cardNumber' => 'required|string|max:20',
            'expiryDate' => 'required|string|max:20',
            'cvv' => 'required|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $validator->errors()
            ], 422);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Retrieve the fuel order
            $fuelOrder = Order::find($request->input('orderID'));

            if (!$fuelOrder) {
                return response()->json([
                    'message' => 'Fuel order not found',
                    'error' => ""
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
            $fuelOrder->status = "paid";
            $fuelOrder->save();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Payment made successfully',
                'order' => $fuelOrder
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

    public function getOrders(Request $request)
    {
        $customerID = $request->input("customerID");

        $orders = Order::where("customerID", "=", $customerID)->get();
        return response()->json([
            "message" => "Orders Retrieved Successfully",
            "orders" => $orders
        ]);
    }

    public function submitFeedback(Request $request)
    {
        try {
            // Create the feedback
            $feedback = Feedback::create([
                "userID" => $request->input("userID"),
                "feedback" => $request->input("feedback"),
                "submittedBy" => "Customer"
            ]);

            // Check if feedback was created successfully
            if ($feedback) {
                return response()->json([
                    "status" => "success",
                    "message" => "Feedback submitted successfully",
                    "data" => [
                        "feedbackID" => $feedback->id, // Return the ID of the created feedback
                        "customerID" => $feedback->customerID,
                        "feedback" => $feedback->feedback
                    ]
                ], 201); // 201 Created status code for successful resource creation
            } else {
                throw new Exception("Failed to create feedback");
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                "status" => "error",
                "message" => "Failed to submit feedback",
                "error" => $e->getMessage() // Include error message for debugging (remove in production)
            ], 500); // 500 Internal Server Error for unexpected errors
        }
    }
}
