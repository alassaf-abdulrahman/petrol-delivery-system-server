<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Order;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    // Get Fuel Orders Assigned to Driver
    public function getFuelOrders(Request $request)
    {
        $orders = Order::where('driverID', $request->input('driverID'))
            ->where('status', '=', "Pending Driver Confirmation")
            ->orWhere('status', '=', "Delivering")->get();
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

    public function submitFeedback(Request $request)
    {
        try {
            // Create the feedback
            $feedback = Feedback::create([
                "userID" => $request->input("userID"),
                "feedback" => $request->input("feedback"),
                "submittedBy" => "Driver"
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
