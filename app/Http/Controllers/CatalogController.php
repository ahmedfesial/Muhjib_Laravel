<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatalogRequest;
use App\Http\Resources\CatalogResource;
use App\Models\Catalog;
use App\Models\Basket;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;


class CatalogController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', Catalog::class);
        $catalogs = Catalog::with(['basket', 'template'])->latest()->get();
        $data =CatalogResource::collection($catalogs);
        return response()->json([
            'message' => 'Catalogs Retrieved Successfully',
            'data' =>$data
        ],200);
    }

    public function store(StoreCatalogRequest $request)
{
    // $this->authorize('create', Catalog::class);

    $basket = Basket::with(['products'])->findOrFail($request->basket_id);
    $template = Template::findOrFail($request->template_id);

    // PDF Generation Logic
    if (!view()->exists('pdf.templates.custom_template')) {
        return response()->json(['error' => 'PDF view not found'], 404);
    }

    $pdf = PDF::loadView('pdf.templates.custom_template', [
        'basket' => $basket,
        'template' => $template,
    ]);

    $pdfPath = 'catalogs/' . Str::uuid() . '.pdf';
    Storage::put("public/$pdfPath", $pdf->output());

    $catalog = Catalog::create([
        'name' => $request->name,
        'basket_id' => $request->basket_id,
        'template_id' => $request->template_id,
        'created_by' => Auth::id(),
        'pdf_path' => $pdfPath,
    ]);

    $data = new CatalogResource($catalog);

    return response()->json([
        'message' => 'Catalog Created Successfully',
        'data' => $data
    ], 201);
}
    public function show(Catalog $catalog)
    {
        // $this->authorize('view', $id);
        // $catalog= Catalog::find($id);
        // if(!$catalog){
        //     return response()->json([
        //         'message' => 'Catalog not found.',
        //     ], 404);
        // }
        $data=new CatalogResource($catalog);
        return response()->json([
            'message' => 'Catalog Retrieved Successfully',
            'data' => $data
        ],200);
    }

    public function generateCatalog(Request $request)
{
    $request->validate([
        'template_id' => 'required|exists:templates,id',
        'products' => 'required|array',
        'products.*' => 'exists:products,id',
    ]);

    $template = Template::findOrFail($request->template_id);
    $user = Auth::user();
    $products = Product::whereIn('id', $request->products)->get();

    $catalog = Catalog::create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'basket_id' => $request->basket_id,
        'title' => 'My Custom Catalog',
    ]);

    $pdf = PDF::loadView('templates.template_1', [
        'user' => $user,
        'products' => $products,
        'template' => $template,
        'catalog' => $catalog,
    ]);

    return $pdf->download('catalog.pdf');
}

    // public function update(UpdateCatalogRequest $request, Catalog $catalog)
    // {
    //     $this->authorize('update', $catalog);
    //     $catalog = Catalog::find($catalog);
    //     if(!$catalog){
    //         return response()->json([
    //             'message' => 'Catalog not found.',
    //         ], 404);
    //     }
    //     $catalog->update($request->validated());
    //     $data =new CatalogResource($catalog);
    //     return response()->json([
    //         'message' => 'Catalog Updated Successfully',
    //         'data' => $data
    //     ],200);
    // }

    // public function destroy(Catalog $catalog)
    // {
    //     $this->authorize('delete', $catalog);
    //     $catalog = Catalog::find($catalog);
    //     if(!$catalog){
    //         return response()->json([
    //             'message' => 'Catalog not found.',
    //         ], 404);
    //     }
    //     $catalog->delete();
    //     return response()->json(['message' => 'Catalog Deleted Successfully'],200);
    // }
}
