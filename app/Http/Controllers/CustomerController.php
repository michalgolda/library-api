<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Interfaces\CustomerRepositoryInterface;
use Illuminate\Routing\Controller;

class CustomerController extends Controller
{
    private $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $existingCustomers = $this->customerRepository->all();
        $serializedCustomers = CustomerResource::collection($existingCustomers);
        return response()->json(['data' => $serializedCustomers]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $requestData = $request->validated();
        $this->customerRepository->create($requestData);
        return response()->json(['message' => 'Customer successfully created'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($customerId)
    {
        $existingCustomer = $this->customerRepository->getById($customerId);
        if (!$existingCustomer)
            return response()->json(['message' => 'Customer not found'], 404);


        $serializedCustomer = new CustomerResource($existingCustomer);
        return response()->json(['data' => $serializedCustomer]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($customerId)
    {
        $existingCustomer = $this->customerRepository->getById($customerId);
        if (!$existingCustomer)
            return response()->json(['message' => 'Customer not found'], 404);

        $this->customerRepository->delete($customerId);

        return response()->json(['message' => 'Customer successfully deleted']);
    }
}
