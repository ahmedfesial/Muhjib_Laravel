<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientsRequest;
use App\Http\Requests\UpdateClientsRequest;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use App\Models\ClientFile;
use SimpleSoftwareIO\QrCode\Facades\QrCode;



class ClientsController extends Controller
{
    // use AuthorizesRequests;
    public function index(Request $request)
{
    $user = Auth::user();

    // لو المستخدم Super Admin، يقدر يشوف كل العملاء
    if ($user->role === 'super_admin') {
        $clients = Client::paginate(10);
    } else {
        // المستخدم العادي يشوف بس العملاء الموافق عليهم
        $clients = Client::where('status', 'approved')->paginate(10);
    }
    $data =ClientResource::collection($clients);
    return response()->json([
        'message' => 'Clients Retrieved Successfully',
        'data' => $data,
    ], 200);
}


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


    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'nullable|email',
        'phone' => 'nullable|string',
        'company' => 'nullable|string',
        'default_price_type' => 'nullable|in:A,B,C,D',
        'status' => 'nullable|in:pending,approved,rejected',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

$priceType = $validated['default_price_type'] ?? 'A'; // لو مش موجود، نخليها 'A'
$status = $priceType !== 'A' ? 'pending' : 'approved';

    // Upload logo if exists
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('logos', 'public');
        $validated['logo'] = $logoPath;
    }

    $client = Client::create([
        ...$validated,
        'created_by_user_id' => Auth::id(),
        'status' => $status,
    ]);

    // Send notification to super admins if status is pending
    if ($status === 'pending') {
        $superAdmins = User::where('role', 'super_admin')->get();

        foreach ($superAdmins as $admin) {
            Notification::create([
                'type' => 'client_approval_request',
                'sender_id' => Auth::id(),
                'receiver_id' => $admin->id,
                'content' => 'A new client with non-default price type was created. Please review.',
                'related_entity_id' => $client->id,
            ]);
        }
    }

    return response()->json([
        'message' => $status === 'pending'
            ? 'Client created and pending approval.'
            : 'Client created successfully.',
        'data' => $client
    ], 201);
}


public function approve($id)
{
    $client = Client::findOrFail($id);

    if ($client->status !== 'pending') {
        return response()->json(['message' => 'Client is not pending.'], 400);
    }

    $client->update(['status' => 'approved']);

    return response()->json(['message' => 'Client approved successfully.']);
}

public function reject($id)
{
    $client = Client::findOrFail($id);

    if ($client->status !== 'pending') {
        return response()->json(['message' => 'Client is not pending.'], 400);
    }

    $client->update(['status' => 'rejected']);

    return response()->json(['message' => 'Client rejected.']);
}


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

   public function update(UpdateClientsRequest $request, Client $client)
{
    $validated = $request->validated();

    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('clients/logos', 'public');
    }

    // لو المستخدم بعت status يدويًا، هنستخدمه
    // غير كده، نحسبه تلقائيًا بناء على default_price_type
    if (!isset($validated['status'])) {
        $priceType = $validated['default_price_type'] ?? 'A';
        $validated['status'] = $priceType !== 'A' ? 'pending' : 'approved';
    }


    $client->update($validated);

    // إرسال إشعار فقط لو الحالة الجديدة "pending"
    if ($validated['status'] === 'pending') {
        $superAdmins = User::where('role', 'super_admin')->get();

        foreach ($superAdmins as $admin) {
            Notification::create([
                'type' => 'client_approval_request',
                'sender_id' => Auth::id(),
                'receiver_id' => $admin->id,
                'content' => 'Client updated with non-default price type. Please review.',
                'related_entity_id' => $client->id,
            ]);
        }
    }
    $data =new ClientResource($client);

    return response()->json([
        'message' => $validated['status'] === 'pending'
            ? 'Client updated and pending approval.'
            : 'Client updated successfully.',
        'data' => $data
    ], 200);
}

// Company Folder
public function uploadFiles(Request $request, $clientId)
{
    $request->validate([
        'files' => 'required|array',
        'files.*' => 'file|mimes:jpeg,jpg,png,gif,mp4,mov,avi,wmv,pdf,xlsx,xls|max:10240',
    ]);

    $client = Client::findOrFail($clientId);
    $uploadedFiles = [];

    foreach ($request->file('files') as $file) {
        $path = $file->store('client_files', 'public');

        $clientFile = ClientFile::create([
            'client_id' => $client->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
        ]);

        $uploadedFiles[] = $clientFile;
    }

    return response()->json([
        'message' => 'Files uploaded successfully.',
        'data' => $uploadedFiles
    ], 201);
}
public function getClientFiles($clientId)
{
    $client = Client::findOrFail($clientId);
    $files = $client->files;

    return response()->json([
        'message' => 'Client files retrieved successfully.',
        'data' => $files
    ]);
}

// QR Code
public function generateQr($clientId)
{
    $client = Client::findOrFail($clientId);

    $qrContent = url("/client-folder/{$client->id}");

    $qr = QrCode::format('svg')->size(200)->generate($qrContent);
return response($qr)->header('Content-Type', 'image/svg+xml');

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
