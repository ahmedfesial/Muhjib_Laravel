<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientsRequest;
use App\Http\Requests\UpdateClientsRequest;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
class ClientsController extends Controller
{
    use AuthorizesRequests;
<<<<<<< HEAD
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Client::class);
         $query = $this->filter($request);
=======
    public function index()
    {
        // $this->authorize('viewAny', Client::class);
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        $data =ClientResource::collection(Client::paginate(10));
        return response()->json([
            'message' => 'Clients Retrieved Successfully',
            'data' => $data
        ],200);
    }

<<<<<<< HEAD
    private function filter(Request $request)
{
    $query = Client::query();

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('email')) {
        $query->where('email', 'like', '%' . $request->email . '%');
    }

    if ($request->filled('phone')) {
        $query->where('phone', 'like', '%' . $request->phone . '%');
    }

    if ($request->filled('company')) {
        $query->where('company', 'like', '%' . $request->company . '%');
    }

    if ($request->filled('created_by_user_id')) {
        $query->where('created_by_user_id', $request->created_by_user_id);
    }

    return $query;
}

=======
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
    public function store(StoreClientsRequest $request)
    {
        // $this->authorize('create', Client::class);
        $client = Client::create($request->validated());
        $validated['created_by_user_id'] = Auth::id();
<<<<<<< HEAD
        // Handle file upload
    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('clients/logos', 'public');
    }
=======
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e

        $data =new ClientResource($client);
        return response()->json([
            'message' => 'Clients Created Successfully',
            'data' => $data
        ],201);
    }

<<<<<<< HEAD
    public function show($id)
    {
        // $this->authorize('view', $id);
        $client = Client::find($id);
        if(!$client){
            return response()->json([
                'message' => 'Client not found.',
            ], 404);
        }
        $data =new ClientResource($client);
        return response()->json([
                'message' => 'Client Retrieved Successfully ',
                'data' => $data
            ], 200);
    }

    public function update(UpdateClientsRequest $request,Client $id)
    {
        // $this->authorize('update', $client);
        $client = Client::find($id);
        // if(!$client){
        //     return response()->json([
        //     'message' => 'Clients NOt Found'
        // ],404);
        // }
        $validatedData = $request->validated();
        // Handle optional logo update
    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('clients/logos', 'public');
    }

        $client->update($validatedData);
=======
    public function show(Client $client)
    {
        // $this->authorize('view', $client);
        return new ClientResource($client);
    }

    public function update(UpdateClientsRequest $request, Client $client)
    {
        // $this->authorize('update', $client);

        if(!$client){
            return response()->json([
            'message' => 'Clients NOt Found'
        ],404);
        }
        $client->update($request->validated());
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        $data =new ClientResource($client);
        return response()->json([
            'message' => 'Clients Updated Successfully',
            'data' => $data
        ],200);
    }

    public function destroy(Client $client)
    {
        // $this->authorize('delete', $client);
        // if(!$client){
        //     return response()->json([
        //     'message' => 'Clients NOt Found'
        // ],404);
        // }
        $client->delete();
        return response()->json(['message' => 'Client deleted successfully'],200);
    }
}
