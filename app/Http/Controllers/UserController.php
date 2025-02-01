<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        if ($user->role == 'admin') {
            $roleAccount = Admin::where('userID', $user->userID)->first();
        } elseif ($user->role == 'driver') {
            $roleAccount = Driver::where('userID', $user->userID)->first();
        } elseif ($user->role == 'customer') {
            $roleAccount = Customer::where('userID', $user->userID)->first();
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'roleAccount' => $roleAccount
        ], 200);
    }

    // Create a new user account
    public function createUserAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,driver,customer',
            'phoneNumber' => 'required|string|max:15',
            'licenseNumber' => 'required_if:role,driver|string|max:255',
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
            // Create the user
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role'),
                'phoneNumber' => $request->input('phoneNumber'),
            ]);

            // Create role-specific records
            $role = $request->input('role');
            $roleRecord = null;

            switch ($role) {
                case 'admin':
                    $roleRecord = Admin::create([
                        'userID' => $user->userID
                    ]);
                    break;
                case 'driver':
                    $roleRecord = Driver::create([
                        'userID' => $user->userID,
                        'name' => $request->input('name'),
                        'licenseNumber' => $request->input('licenseNumber')
                    ]);
                    $truck = Truck::create([
                        'driverID' => $roleRecord->driverID,
                        'licensePlate' => $request->input('licensePlate'),
                        'safetyCertified' => true,
                    ]);
                    break;
                case 'customer':
                    $roleRecord = Customer::create([
                        'userID' => $user->userID,
                        'name' => $request->input('name')
                    ]);
                    break;
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'User account created successfully',
                'user' => $user,
                strtolower($role) => $roleRecord
            ], 201);

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            return response()->json([
                'message' => 'User account creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update a user account
    public function updateUserAccount(Request $request, $userID)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userID . ',userID',
            'phoneNumber' => 'required|string|max:15',
            'password' => 'string|min:8',
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
            // Update the user
            $user = User::find($userID);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phoneNumber = $request->input('phoneNumber');
            if ($request->has('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->save();

            // Update role-specific records
            $role = $user->role;
            $roleRecord = null;

            switch ($role) {
                case 'admin':
                    $roleRecord = Admin::where('userID', $userID)->first();
                    break;
                case 'driver':
                    $roleRecord = Driver::where('userID', $userID)->first();
                    $roleRecord->name = $request->input('name');
                    $roleRecord->save();
                    break;
                case 'customer':
                    $roleRecord = Customer::where('userID', $userID)->first();
                    $roleRecord->name = $request->input('name');
                    $roleRecord->save();
                    break;
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'User account updated successfully',
                'user' => $user,
                strtolower($role) => $roleRecord
            ], 200);

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            return response()->json([
                'message' => 'User account update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
