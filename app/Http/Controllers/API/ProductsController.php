<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
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

            $products = Product::select('id', 'name', 'guj_name', DB::raw("CONCAT('$APP_URL', photo) as IMAGE"), 'photo')
                ->get()
                ->toArray();

            if (count($products) <= 0) {
                return \generateResponse(false, 'There are no Products available. Please add new product', [], 200);
            }

            return \generateResponse(true, 'Products list has been retrive', $products, 200);
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
     * @param  App\Http\Requests\CustomersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $image_path_with_name = 'uploads/product.jpg';

            if ($request->hasFile('photo')) {
                $image_response = \uploads($request->file('photo'), 'uploads/products/');

                if ($image_response['success']) {
                    $image_path_with_name = $image_response['file_name'];
                } else {
                    return response()->json(['success' => false, 'message' => 'Something was wrong in uploading Image.!'], 200);
                }
            }

            $product = Product::create([
                'name' => $data['name'],
                'guj_name' => $data['guj_name'],
                'photo' => $image_path_with_name,
                'box_price' => $data['box_price'],
            ]);

            if ($product) {
                DB::commit();
                return \generateResponse(true, 'Product has been added successfully', ['id' => $product->id], 200);
            } else {
                DB::rollback();
                return \generateResponse(false, 'Product has not been added successfully', [], 500);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        try {
            $product = $product->toArray();
            $APP_URL = env('APP_URL');

            $product['photo'] = $APP_URL . $product['photo'];

            return \generateResponse(true, 'Products details has been retrive', $product, 200);
        } catch (\Exception $e) {
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        try {
            if ($request->hasFile('photo')) {
                $image_response = \uploads($request->file('photo'), 'uploads/products/');

                if ($image_response['success']) {
                    $image_path_with_name = $image_response['file_name'];
                    if (\file_exists(public_path($product->photo)) && $product->photo != 'uploads/product.jpg') {
                        \remove_uploads($product->photo);
                    }
                    $product->photo = $image_path_with_name;
                } else {
                    return response()->json(['success' => false, 'message' => 'Something was wrong in uploading Image.!'], 200);
                }
            }
            $product->name = $request->get('name');
            $product->guj_name = $request->get('guj_name');
            $product->box_price = $request->get('box_price');

            $product->save();
            $APP_URL = env('APP_URL');
            $product['photo'] = $APP_URL . $product['photo'];
            return \generateResponse(true, 'Product has been updated', $product->toArray(), 200);
        } catch (\Exception $e) {
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            if (\file_exists(public_path($product->photo)) && $product->photo != 'uploads/product.jpg') {
                \remove_uploads($product->photo);
            }
            $product->delete();
            return \generateResponse(true, 'Product has been deleted', [], 200);
        } catch (\Exception $e) {
            return \generateResponse(false, $e->getMessage() . ' In File ' . $e->getFile() . ' On Line ' . $e->getLine(), [], 500);
        }
    }
}
