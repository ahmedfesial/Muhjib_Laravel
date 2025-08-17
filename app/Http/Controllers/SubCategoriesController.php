<?php

namespace App\Http\Controllers;

use App\Models\SubCategories;
use App\Http\Requests\StoreSubCategoriesRequest;
use App\Http\Requests\UpdateSubCategoriesRequest;
use App\Http\Resources\SubCategoryResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubCategoriesController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', SubCategories::class);
        $subcategory = SubCategories::all();
        $data =SubCategoryResource::collection($subcategory);
        return response()->json(['message' => 'Sub Categories Retrieved Successfully', 'data'=>$data],200);
    }

    public function store(StoreSubCategoriesRequest $request)
    {
        // $this->authorize('create', SubCategories::class);
        $subcategory = SubCategories::create($request->validated());
        $data=new SubCategoryResource($subcategory);
        return response()->json(['message'=>'Sub Category Created Successfully', 'data'=>$data],201);
    }

<<<<<<< HEAD
    public function show($id)
    {
        // $this->authorize('view', $subCategory);
        $subCategory = SubCategories::find($id);
        if(!$subCategory){
            return response()->json([
                'message' => 'Sub Category not found.',
            ], 404);
        }
=======
    public function show(SubCategories $subCategory)
    {
        // $this->authorize('view', $subCategory);
        // $subCategory = SubCategories::find($subCategory);
        // if(!$subCategory){
        //     return response()->json([
        //         'message' => 'Sub Category not found.',
        //     ], 404);
        // }
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        $data =new SubCategoryResource($subCategory);
        return response()->json(['message' => 'Sub Category Retrieved Successfully', 'data'=>$data],200);
    }

    public function update(UpdateSubCategoriesRequest $request, SubCategories $subCategory)
    {
<<<<<<< HEAD
        // $this->authorize('update', $subCategory);
=======
        $this->authorize('update', $subCategory);
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        $subCategory = SubCategories::find($subCategory);
        if(!$subCategory){
            return response()->json([
                'message' => 'Sub Category not found.',
            ], 404);
        }
        $subCategory->update($request->validated());
        $data=new SubCategoryResource($subCategory);
        return response()->json(['message' => 'Sub Category Updated Successfully', 'data'=>$data],200);
    }

    public function destroy(SubCategories $subCategory)
    {
        // $this->authorize('delete', $subCategory);
        // $subCategory = SubCategories::find($subCategory);
        // if(!$subCategory){
        //     return response()->json([
        //         'message' => 'Sub Category not found.',
        //     ], 404);
        // }
        $subCategory->delete();
        return response()->json(['message' => 'Deleted successfully'],200);
    }
}

