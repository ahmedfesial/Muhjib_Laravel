<?php

namespace App\Http\Controllers;

use App\Models\SubCategories;
use App\Http\Requests\StoreSubCategoriesRequest;
use App\Http\Requests\UpdateSubCategoriesRequest;
use App\Http\Resources\SubCategoryResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class SubCategoriesController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $subcategory = SubCategories::all();
        $data =SubCategoryResource::collection($subcategory);
        return response()->json(['message' => 'Sub Categories Retrieved Successfully', 'data'=>$data],200);
    }

    public function store(StoreSubCategoriesRequest $request)
    {
        $subcategory = SubCategories::create($request->validated());
        $data=new SubCategoryResource($subcategory);
        return response()->json(['message'=>'Sub Category Created Successfully', 'data'=>$data],201);
    }

    public function show($id)
    {
        $subCategory = SubCategories::find($id);
        if(!$subCategory){
            return response()->json([
                'message' => 'Sub Category not found.',
            ], 404);
        }
        $data =new SubCategoryResource($subCategory);
        return response()->json(['message' => 'Sub Category Retrieved Successfully', 'data'=>$data],200);
    }

public function update(UpdateSubCategoriesRequest $request, SubCategories $subCategory)
{
    $subCategory->update($request->except(['cover_image', 'background_image']));

    if ($request->hasFile('cover_image')) {
        if ($subCategory->cover_image && Storage::disk('public')->exists($subCategory->cover_image)) {
            Storage::disk('public')->delete($subCategory->cover_image);
        }

        $subCategory->cover_image = $request->file('cover_image')->store('subcategories/covers', 'public');
    }

    if ($request->hasFile('background_image')) {
        if ($subCategory->background_image && Storage::disk('public')->exists($subCategory->background_image)) {
            Storage::disk('public')->delete($subCategory->background_image);
        }

        $subCategory->background_image = $request->file('background_image')->store('subcategories/backgrounds', 'public');
    }

    $subCategory->save();

    $data = new SubCategoryResource($subCategory);

    return response()->json([
        'message' => 'Sub Category Updated Successfully',
        'data' => $data
    ], 200);
}

    public function destroy(SubCategories $subCategory)
    {
        $subCategory->delete();
        return response()->json(['message' => 'Deleted successfully'],200);
    }


    public function updateSubCategoryImages(Request $request, SubCategories $subCategory)
{
    $request->validate([
        'cover_image' => 'nullable|image',
        'background_image' => 'nullable|image',
    ]);

    if ($request->hasFile('cover_image')) {
        $subCategory->cover_image = $request->file('cover_image')->store('subcategories/covers', 'public');
    }

    if ($request->hasFile('background_image')) {
        $subCategory->background_image = $request->file('background_image')->store('subcategories/backgrounds', 'public');
    }

    $subCategory->save();

    return response()->json(['message' => 'Images updated']);
}

}

