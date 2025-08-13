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
    public function index()
    {
        // $this->authorize('viewAny', Client::class);
        $data =ClientResource::collection(Client::paginate(10));
        return response()->json([
            'message' => 'Clients Retrieved Successfully',
            'data' => $data
        ],200);
    }

    public function store(StoreClientsRequest $request)
    {
        // $this->authorize('create', Client::class);
        $client = Client::create($request->validated());
        $validated['created_by_user_id'] = Auth::id();

        $data =new ClientResource($client);
        return response()->json([
            'message' => 'Clients Created Successfully',
            'data' => $data
        ],201);
    }

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
