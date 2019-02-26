<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomersRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $APP_URL = env('APP_URL');
            
            $customers = Customer::select('id', 'full_name', 'mobile', DB::raw("CONCAT('$APP_URL', photo) as IMAGE"), 'balance')
                ->get()
                ->toArray();

            if (count($customers) <= 0) {
                return \generateResponse(false, 'There are no customers available. Please add new customer', [], 200);
            }

            return \generateResponse(true, 'Customers list has been retrive', $customers, 200);
        } catch (\Exception $e) {
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CustomersRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomersRequest $request)
    {
        DB::beginTransaction();
        try {
            $data                 = $request->all();
            $image_path_with_name = 'uploads/user.png';

            if ($request->hasFile('photo')) {
                $image_response = \uploads($request->file('photo'), 'uploads/customers/');

                if ($image_response['success']) {
                    $image_path_with_name = $image_response['file_name'];
                } else {
                    return response()->json(['success' => false, 'message' => 'Something was wrong in uploading Image.!'], 200);
                }
            }

            $customer = Customer::create([
                'full_name' => $data['full_name'],
                'mobile'    => $data['mobile'],
                'photo'     => $image_path_with_name,
                'balance'   => $data['balance'],
            ]);

            if ($customer) {
                DB::commit();
                return \generateResponse(true, 'Customer has been added successfully', ['id' => $customer->id], 200);
            } else {
                DB::rollback();
                return \generateResponse(false, 'Customer has not been added successfully', [], 500);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        try {
            $customer = $customer->toArray();
            $APP_URL  = env('APP_URL');

            $customer['photo'] = $APP_URL . $customer['photo'];

            return \generateResponse(true, 'Customers details has been retrive', $customer, 200);
        } catch (\Exception $e) {
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            if ($request->hasFile('photo')) {
                $image_response = \uploads($request->file('photo'), 'uploads/customers/');

                if ($image_response['success']) {
                    $image_path_with_name = $image_response['file_name'];
                    if (\file_exists(public_path($customer->photo)) && $customer->photo != 'uploads/user.png') {
                        \remove_uploads($customer->photo);
                    }
                    $customer->photo = $image_path_with_name;
                } else {
                    return response()->json(['success' => false, 'message' => 'Something was wrong in uploading Image.!'], 200);
                }
            }

            $customer->full_name = $request->get('full_name');
            $customer->mobile    = $request->get('mobile');
            $customer->balance   = $request->get('balance');

            $customer->save();
            $APP_URL           = env('APP_URL');
            $customer['photo'] = $APP_URL . $customer['photo'];
            return \generateResponse(true, 'Customer has been updated', $customer->toArray(), 200);

        } catch (\Exception $e) {
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        try {
            if (\file_exists(public_path($customer->photo)) && $customer->photo != 'uploads/user.png') {
                \remove_uploads($customer->photo);
            }
            $customer->delete();
            return \generateResponse(true, 'Customer has been deleted', [], 200);
        } catch (\Exception $e) {
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }
}
