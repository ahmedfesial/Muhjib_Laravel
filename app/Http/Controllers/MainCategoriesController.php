<?php
namespace App\Http\Controllers;

use App\Models\MainCategories;
use App\Http\Requests\StoreMainCategoriesRequest;
use App\Http\Requests\UpdateMainCategoriesRequest;
use App\Http\Resources\MainCategoryResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MainCategoriesController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', MainCategories::class);
        $mainCategory = MainCategories::all();
        $data =MainCategoryResource::collection($mainCategory);
        return response()->json(['message' => 'Main Categories Retrieved Successfully', 'data'=>$data],200);
    }
    public function filter(){
    }

    public function store(StoreMainCategoriesRequest $request)
    {
        // $this->authorize('create', MainCategories::class);
        $data = $request->validated();
        // Image handle
        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->store('main_categories', 'public');
        }

        $category = MainCategories::create($data);
        $data = new MainCategoryResource($category);
        return response()->json(['message'=>'Main Category Created Successfully', 'data'=>$data],201);
    }

    public function show($id)
    {
        // $this->authorize('view', $mainCategory);
        $mainCategory = MainCategories::find($id);
        if(!$mainCategory){
            return response()->json([
                'message' => 'Main Category not found.',
            ], 404);
        }
        $data =new MainCategoryResource($mainCategory);
        return response()->json(['message' => 'Main Category Retrieved Successfully', 'data'=>$data],200);
    }

    public function update(UpdateMainCategoriesRequest $request, MainCategories $mainCategory)
    {
        $this->authorize('update', $mainCategory);
        if(!$mainCategory){
            return response()->json([
                'message' => 'Main Category not found.',
            ], 404);
        }
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('main_categories', 'public');
        }

        $mainCategory->update($data);
        $datashow = new MainCategoryResource($mainCategory);
        return response()->json(['message' => 'Main Category Updated Successfully', 'data'=>$datashow],200);
    }

    public function destroy(MainCategories $mainCategory)
    {
        // $this->authorize('delete', $mainCategory);
        // if(!$mainCategory){
        //     return response()->json([
        //         'message' => 'Main Category not found.',
        //     ], 404);
        // }
        if ($mainCategory->image_url) {
            Storage::disk('public')->delete($mainCategory->image_url);
        }

        $mainCategory->delete();
        return response()->json(['message' => 'Deleted successfully'],200);
    }
}
