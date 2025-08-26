<!DOCTYPE html>
<html>
<head>
    <title>Template PDF</title>
    <style>
        /* أضف CSS هنا لو حابب */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .page {
            page-break-after: always;
            padding: 20px;
        }
        .cover {
            text-align: center;
            background-color: #f0f0f0;
            padding: 50px 20px;
        }
        .cover h1 {
            margin-bottom: 20px;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
        }
        .product {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            width: calc(33% - 40px);
            box-sizing: border-box;
            text-align: center;
        }
        .product img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    {{-- Page 1: Cover Start --}}
<div class="page cover">
    @if($template->cover_image_start)
        <img src="{{ public_path('storage/'.$template->cover_image_start) }}" style="max-width:100%;height:auto;">
    @endif
    <h1>{{ $template->name }}</h1>
    @if($template->logo)
        <img src="{{ public_path('storage/'.$template->logo) }}" width="150">
    @endif
</div>

{{-- Page 2: Client + Created By --}}
<div class="page">
    <h2>Client Information</h2>
    <p><strong>Name:</strong> {{ $client->client_name ?? 'N/A' }}</p>
    <p><strong>Email:</strong> {{ $client->email ?? 'N/A' }}</p>
    <p><strong>Phone:</strong> {{ $client->phone ?? 'N/A' }}</p>
    <p><strong>Address:</strong> {{ $client->address ?? 'N/A' }}</p>

    <h2>Created By</h2>
    <p><strong>User:</strong> {{ $user->name }} </p>
    <p><strong>Email:</strong> {{ $user->email  }}</p>
    <p><strong>Created At:</strong> {{ $template->created_at->format('d M Y') }}</p>

</div>


   {{-- Page 3: Products --}}
<div class="page">
    <h2>Products</h2>
    <div class="products">
        @foreach($templateProducts as $tp)
            <div class="product">
                @if($tp->image)
                    <img src="{{ public_path('storage/'.$tp->image) }}">
                @endif
                <p><strong>{{ $tp->name }}</strong></p>
                <p>{{ $tp->description }}</p>
                <p><strong>Price:</strong> {{ $tp->price }} EGP</p>
            </div>
        @endforeach
    </div>
</div>

    {{-- الغلاف النهائي --}}
    <h2>Thank You</h2>
    <div>
        @if($template->cover_image_end)
            <img src="{{ public_path('storage/' . $template->cover_image_end) }}" alt="Cover End" style="width:100%;">
        @endif
    </div>


</body>
</html>
