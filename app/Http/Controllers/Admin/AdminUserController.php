<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UserTransformer;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!$users = User::with('roles')->get()) {
            throw new NotFoundHttpException('Users not found');
        }

        return $users;
        // return $this->response
        //             ->collection($users, new UserTransformer)
        //             ->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email|max:30',
            'name' => 'required|string|min:3|max:30',
            'firstname' => 'required|string|min:3|max:30',
            'lastname' => 'required|string|min:3|max:30',
            'gender' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation errors' => $validator->errors()
            ]);
        }

        try {
            $user = User::firstOrCreate(['email' => $request->email], [
                'name' => $request->name,
                'email' => $request->email,
                'password' => 'Test@123'
            ]);

            $user->userProfile()->updateOrCreate(['user_id' => $user->id], [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'active' => true
            ]);

            $user->assignRole('customer');
        } catch (HttpException $th) {
            throw $th;
        }

        $response = [
            'message' => 'User created successfully',
            'id' => $user->id
        ];

        return response()->json($response)->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$user = User::find($id)) {
            throw new NotFoundHttpException('User not found with id = ' . $id);
        }

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$user = User::find($id)) {
            throw new NotFoundHttpException('User not found with id = ' . $id);
        }

        if (!empty($request->firstname)) {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|min:3|max:30',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ]);
            }

            $user->userProfile()->updateOrCreate(['user_id' => $user->id], [
                'firstname' => $request->firstname,
            ]);
        }

        if (!empty($request->lastname)) {
            $validator = Validator::make($request->all(), [
                'lastname' => 'required|string|min:3|max:30',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ]);
            }

            $user->userProfile()->updateOrCreate(['user_id' => $user->id], [
                'lastname' => $request->lastname,
            ]);
        }

        $response = [
            'message' => 'User profile updated successfully',
            'id' => $user->id,
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$user = User::find($id)) {
            throw new NotFoundHttpException('User not found');
        }

        try {
            $user->delete();
        } catch (HttpException $th) {
            throw $th;
        }

        return response()->json(['message' => 'User deleted successfully', 'id' => $id]);
    }

    public function suspend($id)
    {
        if (!$user = User::find($id)) {
            throw new NotFoundHttpException('User not found');
        }

        try {
            $user->userProfile->createOrUpdate(['user_id', $id], [
                'active' => false
            ]);
        } catch (HttpException $th) {
            throw $th;
        }

        $response = ['message' => 'User suspended successfully', 'id' => $id];

        return response()->json($response, 200);
    }

    public function activate($id)
    {
        if (!$user = User::find($id)) {
            throw new NotFoundHttpException('User not found');
        }

        try {
            $user->userProfile->createOrUpdate(['user_id', $id], [
                'active' => true
            ]);
        } catch (HttpException $th) {
            throw $th;
        }

        $response = ['message' => 'User suspended successfully', 'id' => $id];

        return response()->json($response, 200);
    }
}
