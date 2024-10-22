<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $existingCustomers = Customer::all();
        return response()->json(['ok' => true, 'data' => CustomerResource::collection($existingCustomers)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        Customer::create($data);
        return response()->json(['ok' => true, 'message' => 'Customer successfully created'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($customerId)
    {
        $existingCustomer = Customer::find($customerId);
        if (!$existingCustomer)
            return response()->json(['ok' => true, 'message' => 'Customer not found'], 404);

        return response()->json(['ok' => true, 'data' => new CustomerResource($existingCustomer)]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($customerId)
    {
        $existingCustomer = Customer::find($customerId);
        if (!$existingCustomer)
            return response()->json(['ok' => true, 'message' => 'Customer not found'], 404);

        $existingCustomer->delete();
        return response()->json(['ok' => true, 'message' => 'Customer successfully deleted']);
    }
}
