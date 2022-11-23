<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\ProductLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($storeId, $brandId)
    {
        $productLines = ProductLine::whereHas('brands.stores', function ($query) use ($storeId, $brandId) {
            $query->where('store_id', $storeId);
            $query->where('brand_id', $brandId);
        })->get();

        if (empty($productLines)) {
            throw new NotFoundHttpException('Product lines does not exist for brand');
        }

        return $productLines;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $storeId, $brandId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:30',
            'details' => 'required|string|min:15|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ]);
        }

        $brand = Brand::whereHas('stores', function ($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })->find($brandId);

        if (empty($brand)) {
            throw new NotFoundHttpException('Brand does not exist');
        }

        $productLine = $brand->productLines()->create([
            'name' => $request->name,
            'details' => $request->details
        ]);


        $response = [
            'message' => 'Productline created successfully',
            'id' => (int) $productLine->id
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductLine  $productLine
     * @return \Illuminate\Http\Response
     */
    public function show($storeId, $brandId, $id)
    {
        $productLines = ProductLine::whereHas('brands.stores', function ($query) use ($storeId, $brandId) {
            $query->where('store_id', $storeId);
            $query->where('brand_id', $brandId);
        })->find($id);

        if (!$productLines) {
            throw new NotFoundHttpException('Product lines does not exist for brand');
        }

        return $productLines;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductLine  $productLine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $storeId, $brandId, $id)
    {
        $productLine = ProductLine::whereHas('brands.stores', function ($query) use ($storeId, $brandId) {
            $query->where('store_id', $storeId);
            $query->where('brand_id', $brandId);
        })->find($id);

        if (!$productLine) {
            throw new NotFoundHttpException('Product lines does not exist for brand');
        }

        if (!empty($request->name)) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:30|unique:brands,name'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $productLine->name = $request->name;
        }

        if (!empty($request->details)) {
            $validator = Validator::make($request->all(), [
                'details' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['validation_errors' => $validator->errors()], 400);
            }



            $productLine->details = $request->details;
        }

        try {
            if ($productLine->isDirty()) {

                $productLine->save();

                $response = [
                    'message' => 'Productline updated successfully',
                    'id' => $productLine->id
                ];

                return response()->json($response, 200);
            }

            return response()->json(['message' => 'Nothing to update'], 200);
        } catch (HttpException $th) {
            throw $th;
        }

        return response()->json(['message' => 'Nothing to update'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductLine  $productLine
     * @return \Illuminate\Http\Response
     */
    public function destroy($storeId, $brandId, $id)
    {
        $productLine = ProductLine::whereHas('brands.stores', function ($query) use ($storeId, $brandId) {
            $query->where('store_id', $storeId);
            $query->where('brand_id', $brandId);
        })->find($id);

        if (!$productLine) {
            throw new NotFoundHttpException('Product lines does not exist for brand');
        }

        try {
            $productLine->delete();
        } catch (HttpException $th) {
            throw $th;
        }

        $response = [
            'message' => 'Productline delete successfully',
            'id' => $id,
        ];

        return response()->json($response, 200);
    }
}
