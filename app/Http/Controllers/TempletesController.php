<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Http\Requests\StoreTempletesRequest;
use App\Http\Resources\TemplateResource;
use App\Http\Requests\UpdateTempletesRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class TempletesController extends Controller
{
    use AuthorizesRequests;
    // public function __construct()
    // {
    //     $this->authorizeResource(Template::class, 'template');
    // }

    public function index()
    {
        // $this->authorize('viewAny', Template::class);
        $data =TemplateResource::collection(Template::all());
        return response()->json(['message'=>'Templates Retrieved Successfully', 'data' => $data],200);
    }

    public function store(StoreTempletesRequest $request)
    {
        // $this->authorize('create', Template::class);
        $path = $request->file('file_path')->store('templates', 'public');
        $template = Template::create($request->validated());
        $data =new TemplateResource($template);
        return response()->json(['message'=>'Template Created Successfully', 'data' => $data],201);
    }

    public function show(Template $template)
    {
        $template = Template::find($template);
        if(!$template){
            return response()->json([
                'message' => 'Brand not found.',
            ], 404);
        }
        $data =new TemplateResource($template);
        return response()->json(['message'=>'Template Retrieved Successfully', 'data' => $data],200);
    }

    public function destroy(Template $template)
    {
        // $template = Template::find($template);
        // if(!$template){
        //     return response()->json([
        //         'message' => 'Brand not found.',
        //     ], 404);
        // }
        $template->delete();
        return response()->json(['message' => 'Template deleted successfully']);
    }
    public function editTemplate(){
        // Edit Template for client product and user can dowmload it
        
    }
}
