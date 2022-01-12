<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class ProductController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->user
            ->products()
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'price' => 'required|numeric',
            'qty' => 'required|integer',
            'mfd' => 'required|date_format:Y-m-d',
            'category' => 'required',
            'image' => 'required|image',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false,'error' => $validator->messages()], 200);
        }


        $img = $request->file('image')->storePublicly('image');


        //Request is valid, create new product
        $product = $this->user->products()->create([
            'title' => $request->get('title'),
            'price' => $request->get('price'),
            'qty' => $request->get('qty'),
            'mfd' => $request->get('mfd'),
            'category' => $request->get('category'),
            'image' => $img,
        ]);

        //Product created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = $this->user->products()->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product not found.'
            ], 200);
        }

        return $product;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //Validate data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'price' => 'required|numeric',
            'qty' => 'required|integer',
            'mfd' => 'required|date_format:Y-m-d',
            'category' => 'required',
            'image' => 'image',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false,'error' => $validator->messages()->first()], 200);
        }

        $img = ($request->hasFile('image')) ?
            $request->file('image')->storePublicly('image') :
            $product->image;

        //Request is valid, update product
        $product = $product->update([
            'title' => $request->get('title'),
            'price' => $request->get('price'),
            'qty' => $request->get('qty'),
            'mfd' => $request->get('mfd'),
            'category' => $request->get('category'),
            'image' => $img,
        ]);

        //Product updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
