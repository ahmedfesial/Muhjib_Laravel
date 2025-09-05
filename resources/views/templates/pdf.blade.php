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
            padding: 10px 10px;
        }
        .cover h1 {
            margin-bottom: 20px;
        }
        .products {
            display: flex;
            flex-wrap: wrap; /* عشان لو العدد كبير يتنقل لسطر جديد */
            justify-content: center; /* عشان يجو في النص */
            gap: 20px; /* مسافة بين الكروت */
        }

        .product {
            width: 250px; /* عرض الكارت، ممكن تزوده أو تنقصه */
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            box-sizing: border-box;
            text-align: center;
        }



        .product img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .logo-top-right {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
        }
        .centered-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            position: relative;
        }

    </style>
</head>
<body>
 {{-- Cover Start Images --}}
    <div class="page cover centered-content">

        @foreach($template->startCoverImages as $cover)
                <img src="{{ public_path('storage/' . $cover->path) }}" style="max-width:100%; height:auto;">
        @endforeach
    </div>

{{-- Page 2: Client + Created By --}}
<div class="page centered-content">
    <h1>{{ $template->name }}</h1>
    @if($template->logo)
        <img src="{{ public_path('storage/'.$template->logo) }}" class="logo-top-right">
    @endif
    <h2>Client Information</h2>
    <p><strong>Name:</strong> {{ $client->client_name ?? 'N/A' }}</p>
    <p><strong>Email:</strong> {{ $client->email ?? 'N/A' }}</p>
    <p><strong>Phone:</strong> {{ $client->phone ?? 'N/A' }}</p>


    <h2>Created By</h2>
    <p><strong>User:</strong> {{ $user->name }} </p>
    <p><strong>Email:</strong> {{ $user->email  }}</p>
    <p><strong>Created At:</strong> {{ $template->created_at->format('d M Y') }}</p>

</div>


   {{-- Page 3: Products --}}
<div class="page centered-content">
    @if($template->logo)
        <img src="{{ public_path('storage/'.$template->logo) }}" class="logo-top-right">
    @endif
    <h2>Products</h2>
    @foreach($templateProducts as $tp)
    <div class="product centered-content">
        @php
            $relativePath = str_replace(url('/storage') . '/', '', $tp->image);
            $absolutePath = public_path('storage/' . $relativePath);
        @endphp

        @if($tp->image && file_exists($absolutePath))
            <img src="file://{{ $absolutePath }}" style="max-width:100%; height:auto;">
        @else
            <img src="{{ public_path('images/placeholder.jpg') }}" style="max-width:100%; height:auto;">
        @endif

        <p><strong>{{ $tp->name }}</strong></p>
        <p>{{ $tp->description }}</p>
       <p><strong>Price:</strong> {{ $tp->price }} EGP</p>
        <p><strong>Quantity:</strong> {{ $tp->quantity ?? 1 }}</p>
        <p><strong>Total Price:</strong> {{ number_format(($tp->price * ($tp->quantity ?? 1)), 2) }} EGP</p>

        @endforeach

</div>
</div>


    {{-- الغلاف النهائي --}}

{{-- Final Cover Images --}}

<div class="page">
    <div class="page centered-content">
            @if($template->logo)
        <h2>Thank You</h2>
                <img src="{{ public_path('storage/'.$template->logo) }}" class="logo-top-right">
            @endif
        @foreach($template->endCoverImages as $cover)
            <img src="{{ public_path('storage/' . $cover->path) }}" style="width:100%;">
        @endforeach
    </div>
</div>
</body>
</html>
