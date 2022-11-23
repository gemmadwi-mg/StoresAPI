<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!$user = auth()->user()) {
            throw new NotFoundHttpException('User not found');
        }

        $stores = Store::with('owner', 'users')->where('owner_id', $user->id)->get();

        return $stores;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'unique:stores,name'
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

        if (!$user = auth()->user()) {
            throw new NotFoundHttpException('User not found');
        }

        $store = $user->stores()->create([
            'name' => $request->name,
            'details' => $request->details,
        ]);

        $response = [
            'message' => 'Store created successfully',
            'id' => $store->id
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$user = auth()->user()) {
            throw new NotFoundHttpException('User not found');
        }

        $stores = Store::with('owner', 'users')->where('owner_id', $user->id)->find($id);

        return $stores;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStoreRequest  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        if (!$user = auth()->user()) {
            throw new NotFoundHttpException('User not found');
        }

        $store = Store::with('owner', 'users')->where('owner_id', $user->id)->find($id);

        if (!$store) {
            throw new NotFoundHttpException('Store does not exists');
        }

        if (!empty($request->name)) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:30|unique:stores,name'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $store->name = $request->name;
        }

        if (!empty($request->details)) {
            $validator = Validator::make($request->all(), [
                'details' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['validation_errors' => $validator->errors()], 400);
            }

            $store->details = $request->details;
        }

        if ($store->isDirty()) {

            $store->save();

            $response = [
                'message' => 'Store updated successfully',
                'id' => $store->id
            ];

            return response()->json($response, 200);
        }

        return response()->json(['message' => 'Nothing to update'], 200);
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$user = auth()->user()) {
            throw new NotFoundHttpException('User not found');
        }

        $store = Store::where('owner_id', $user->id)->find($id);

        if (!$store) {
            throw new NotFoundHttpException('Store does not exists');
        }

        try {
            $store->delete();
            $response = [
                'message' => 'Store delete successfully',
                'id' => $store->id,
            ];

            return response()->json($response, 200);
        } catch (HttpException $th) {
            throw $th;
        }
    }
}
