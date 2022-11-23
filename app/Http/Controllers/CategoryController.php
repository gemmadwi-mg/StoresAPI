<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();


        if (empty($brands)) {
            throw new NotFoundHttpException('Category does not exist');
        }

        return $categories;
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
            'name' => 'required|string|min:3|max:30',
            'details' => 'required|string|min:15|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $category = Category::create($request->all());
        } catch (HttpException $th) {
            throw $th;
        }

        $response = [
            'message' => 'Category created successfully',
            'id' => $category->id
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categories = Category::find($id);


        if (!$categories) {
            throw new NotFoundHttpException('Category does not exist');
        }

        return $categories;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            throw new NotFoundHttpException('Category does not exist');
        }

        if ($request->name) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:30',
                'details' => 'required|string|min:15|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $category->name = $request->name;
        }

        if ($request->details) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:30',
                'details' => 'required|string|min:15|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $category->details = $request->details;
        }

        try {
            if ($category->isDirty()) {

                $category->save();

                $response = [
                    'message' => 'Brand updated successfully',
                    'id' => $category->id
                ];

                return response()->json($response, 200);
            }

            return response()->json(['message' => 'Nothing to update'], 200);
        } catch (HttpException $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            throw new NotFoundHttpException('Category does not exist');
        }

        try {
            $category->delete();
        } catch (HttpException $th) {
            throw $th;
        }

        $response = [
            'message' => 'category delete successfully',
            'id' => $id,
        ];

        return response()->json($response, 200);
    }
}
