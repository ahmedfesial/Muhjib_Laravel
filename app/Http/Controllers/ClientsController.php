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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;


class ClientsController extends Controller
{
    // use AuthorizesRequests;
public function index(Request $request)
{
    $user = Auth::user();

    if ($user->role === 'super_admin') {
        // رجع كل الكلاينت بدون شروط
        $clients = Client::paginate(10);
    } else {
        // العملاء الموافق عليهم ولهم Quote Approved فقط
        $clients = Client::where('status', 'approved')
                         ->whereHas('quoteRequest', function ($query) {
                             $query->where('status', 'approved');
                         })->paginate(10);
    }

    $data = ClientResource::collection($clients);

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

public function createClientSubfolder(Request $request, $clientId)
{
    $client = Client::findOrFail($clientId);

    $baseFolderPath = storage_path("app/public/client_files/{$client->id}/");

    // لو مش موجود الاسم، نولده تلقائيًا
    $folderName = $request->folder_name;

    if (!$folderName) {
        $counter = 1;
        do {
            $folderName = "New Folder {$counter}";
            $subfolderPath = $baseFolderPath . $folderName;
            $counter++;
        } while (File::exists($subfolderPath));
    } else {
        $subfolderPath = $baseFolderPath . $folderName;
    }

    if (File::exists($subfolderPath)) {
        return response()->json(['message' => 'Folder already exists.'], 409);
    }

    File::makeDirectory($subfolderPath, 0755, true);

    return response()->json([
        'message' => 'Folder created successfully.',
        'folder_name' => $folderName,
        'path' => "client_files/{$client->id}/{$folderName}",
    ], 201);
}
public function renameClientFolder(Request $request, $clientId)
{
    $request->validate([
        'old_name' => 'required|string',
        'new_name' => 'required|string',
    ]);

    $client = Client::findOrFail($clientId);

    $basePath = storage_path("app/public/client_files/{$client->id}/");

    $oldPath = $basePath . $request->old_name;
    $newPath = $basePath . $request->new_name;

    if (!File::exists($oldPath)) {
        return response()->json(['message' => 'Old folder does not exist.'], 404);
    }

    if (File::exists($newPath)) {
        return response()->json(['message' => 'New folder name already exists.'], 409);
    }

    File::move($oldPath, $newPath);

    return response()->json([
        'message' => 'Folder renamed successfully.',
        'old_name' => $request->old_name,
        'new_name' => $request->new_name,
    ], 200);
}

public function viewClientSubfolder(Request $request, $clientId, $folderName = null)
{
    $client = Client::findOrFail($clientId);

    // لو ما فيش folderName، استخدم المجلد الرئيسي للعميل
    $folderPath = $folderName
        ? "client_files/{$client->id}/{$folderName}"
        : "client_files/{$client->id}";

    $fullPath = storage_path("app/public/" . $folderPath);

    if (!File::exists($fullPath)) {
    return response()->json([
        'message' => 'Folder is empty or does not exist.',
        'folder_name' => $folderName ?? 'Root Folder',
        'data' => []
    ], 200);
    }


    $files = File::files($fullPath);

    $fileData = [];

    foreach ($files as $file) {
        $fileData[] = [
            'file_name' => $file->getFilename(),
            'file_type' => $file->getExtension(),
            'file_url' => asset('storage/' . $folderPath . '/' . $file->getFilename()),
        ];
    }

    return response()->json([
        'message' => 'Folder contents retrieved successfully.',
        'folder_name' => $folderName ?? 'Root Folder',
        'data' => $fileData,
    ]);
}

public function deleteClientFolder(Request $request, $clientId)
{
    $request->validate([
        'folder_name' => 'required|string',
    ]);

    $client = Client::findOrFail($clientId);

    $folderPath = storage_path("app/public/client_files/{$client->id}/" . $request->folder_name);

    if (!File::exists($folderPath)) {
        return response()->json(['message' => 'Folder does not exist.'], 404);
    }

    File::deleteDirectory($folderPath);

    return response()->json(['message' => 'Folder deleted successfully.'], 200);
}


public function uploadFolder(Request $request, $clientId)
{
    $request->validate([
        'folder_zip' => 'required|file|mimes:zip|max:20480', // 20 MB max, عدل حسب الحاجة
    ]);

    $client = Client::findOrFail($clientId);

    $zipFile = $request->file('folder_zip');

    $zipPath = $zipFile->store("client_files/{$client->id}/temp", 'public');

    $fullZipPath = storage_path('app/public/' . $zipPath);

    // فك الضغط
    $zip = new \ZipArchive;
    if ($zip->open($fullZipPath) === TRUE) {
        $extractPath = storage_path("app/public/client_files/{$client->id}/");
        $zip->extractTo($extractPath);
        $zip->close();

        // امسح ملف ZIP بعد ما تفك الضغط لو حابب
        unlink($fullZipPath);

        // ممكن هنا تعالج الملفات وتخزن بياناتها في DB لو حابب
        // مثلا تمشي على كل الملفات المفكوكة وتضيفهم في ClientFile model

        $files = File::allFiles($extractPath);

        $uploadedFiles = [];

        foreach ($files as $file) {
            // خذ اسم الملف فقط بدون المسار الكامل داخل التخزين
            $relativePath = str_replace($extractPath, '', $file->getPathname());

            $clientFile = ClientFile::create([
                'client_id' => $client->id,
                'file_name' => $file->getFilename(),
                'file_path' => "client_files/{$client->id}/" . str_replace('\\', '/', $relativePath),
                'file_type' => $file->getExtension(),
            ]);

            $uploadedFiles[] = $clientFile;
        }

        return response()->json([
            'message' => 'Folder uploaded and extracted successfully.',
            'data' => $uploadedFiles,
        ], 201);
    } else {
        return response()->json([
            'message' => 'Failed to open ZIP file.',
        ], 400);
    }
}

public function uploadFiles(Request $request, $clientId)
{
    $request->validate([
        'files' => 'required|array',
        'files.*' => 'file|max:10240',
        'folder_name' => 'nullable|string',
    ]);

    $client = Client::findOrFail($clientId);
    $uploadedFiles = [];

    $basePath = "client_files/{$client->id}";
    if ($request->filled('folder_name')) {
        $basePath .= '/' . $request->folder_name;
    }

    foreach ($request->file('files') as $file) {
        $path = $file->store($basePath, 'public');

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
public function viewClientFolder($clientId)
{
    $client = Client::findOrFail($clientId);
    $files = $client->files;

    // ضيف رابط التحميل لكل ملف
    $filesData = $files->map(function ($file) {
        return [
            'id' => $file->id,
            'file_name' => $file->file_name,
            'file_type' => $file->file_type,
            'file_url' => asset('storage/' . $file->file_path),
        ];
    });

    return response()->json([
        'message' => 'Client folder retrieved successfully.',
        'data' => [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'files' => $filesData
        ]
    ]);
}
public function getClientFolders($clientId)
{
    // تأكد أن العميل موجود
    $client = Client::findOrFail($clientId);

    // مسار مجلد العميل
    $basePath = storage_path("app/public/client_files/{$client->id}");

    // تأكد أن المجلد موجود
    if (!File::exists($basePath)) {
        return response()->json([
            'message' => 'No folders found.',
            'data' => []
        ], 200);
    }

    // الحصول على كل المجلدات داخل مجلد العميل
    $folders = File::directories($basePath);

    $folderDetails = [];

    foreach ($folders as $folderPath) {
        $folderName = basename($folderPath);

        // الملفات داخل الفولدر
        $files = File::files($folderPath);

        // حجم الفولدر
        $size = 0;
        foreach ($files as $file) {
            $size += $file->getSize();
        }

        $folderDetails[] = [
            'name' => $folderName,
            'relative_path' => "storage/client_files/{$client->id}/{$folderName}",
            'file_count' => count($files),
            'size_kb' => round($size / 1024, 2),
            'last_modified' => Carbon::createFromTimestamp(File::lastModified($folderPath))->toDateTimeString(),
        ];
    }

    return response()->json([
        'message' => 'Client folders retrieved successfully.',
        'data' => $folderDetails
    ], 200);
}
// QR Code
public function generateQr($clientId)
{
    $client = Client::findOrFail($clientId);

    // تأكد إن فيه Route بيروح لـ /client-folder/{id}
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
