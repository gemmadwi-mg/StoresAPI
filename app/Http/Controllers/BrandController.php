<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($storeId)
    {
        $brands = Brand::whereHas('stores', function ($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })->get();

        if (empty($brands)) {
            throw new NotFoundHttpException('Store does not exist');
        }

        return $brands;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $storeId)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'unique:brands,name'
            ],
            'details' => [
                'required',
                'string',
                'max:255'
            ]
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors()
            ]);
        }

        try {
            $brand = Store::find($storeId)->brands()->create($request->all());
        } catch (HttpException $th) {
            throw $th;
        }

        $response = [
            'message' => 'Brand created successfully',
            'id' => $brand->id
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show($storeId, $id)
    {
        $brands = Brand::whereHas('stores', function ($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })->find($id);

        if (empty($brands)) {
            throw new NotFoundHttpException('Store does not exist');
        }

        return $brands;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $storeId, $id)
    {
        $brand = Brand::whereHas('stores', function ($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })->find($id);

        if (empty($brands)) {
            throw new NotFoundHttpException('Brand not found');
        }

        if (!empty($request->name)) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:30|unique:brands,name'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $brand->name = $request->name;
        }

        if (!empty($request->details)) {
            $validator = Validator::make($request->all(), [
                'details' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['validation_errors' => $validator->errors()], 400);
            }



            $brand->details = $request->details;
        }


        try {
            if ($brand->isDirty()) {

                $brand->save();

                $response = [
                    'message' => 'Brand updated successfully',
                    'id' => $brand->id
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
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy($storeId, $id)
    {
        $brand = Brand::whereHas('stores', function ($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })->find($id);

        if (empty($brand)) {
            throw new NotFoundHttpException('Brand not found');
        }

        try {
            $brand->delete();
        } catch (HttpException $th) {
            throw $th;
        }

        $response = [
            'message' => 'brand delete successfully',
            'id' => $id,
        ];

        return response()->json($response, 200);
    }
}
