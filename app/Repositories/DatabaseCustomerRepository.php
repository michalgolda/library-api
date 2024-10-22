<?php

namespace App\Repositories;

use App\Interfaces\CustomerRepositoryInterface;
use App\Models\Customer;

class DatabaseCustomerRepository implements CustomerRepositoryInterface
{
    public function getById(int $id)
    {
        return Customer::find($id);
    }

    public function all(array $filters = [])
    {
        return Customer::all();
    }

    public function create(array $data)
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data)
    {
        $customer = Customer::find($id);
        $customer->first_name = $data['first_name'];
        $customer->last_name = $data['last_name'];
        $customer->save();
        $customer->refresh();
    }

    public function delete(int $id)
    {
        $customer = Customer::find($id);
        $customer->delete();
    }
}
