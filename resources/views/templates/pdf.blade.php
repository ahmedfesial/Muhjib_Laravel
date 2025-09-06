<!DOCTYPE html>
<html>
<head>
    <title>Template PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .page {
            page-break-after: always;
            position: relative;
            padding: 20px;
        }
        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.1;
            z-index: 0;
        }
        .content {
            position: relative;
            z-index: 1;
        }
        .centered-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .logo-top-right {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
        }
        .products {
            justify-content: center;
            text-align: center;
            gap: 20px;
        }
        .product {
            display: inline-block;
            vertical-align: top;
            width: 240px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            box-sizing: border-box;
            text-align: center;
            background-color: #fff;

        }
        .product img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

{{-- ✅ الغلافات (البداية) --}}
@foreach($template->startCoverImages as $cover)
    <div class="page centered-content">
        <img src="{{ public_path('storage/' . $cover->path) }}" style="max-width:100%; height:auto;">
    </div>
@endforeach

{{-- ✅ صفحة بيانات العميل --}}
@php
    $clientBg = $template->coverImages->where('background_position', 'client')->first();
@endphp

<div class="page">
    @if($clientBg)
        <img src="{{ public_path('storage/' . $clientBg->path) }}" class="background-image">
    @endif

    <div class="content centered-content">
        <h1>{{ $template->name }}</h1>
        @if($template->logo)
            <img src="{{ public_path('storage/'.$template->logo) }}" class="logo-top-right">
        @endif

        <h2>Client Information</h2>
        <p><strong>Name:</strong> {{ $client->client_name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $client->email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $client->phone ?? 'N/A' }}</p>

        <h2>Created By</h2>
        <p><strong>User:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Created At:</strong> {{ $template->created_at->format('d M Y') }}</p>
    </div>
</div>

{{-- ✅ المنتجات حسب الـ SubCategory --}}
@php
    $productsBg = $template->coverImages->where('background_position', 'products')->first();
@endphp

@foreach($groupedProducts as $subCategoryId => $products)
    @php
        $subCategory = $products->first()->product->subCategory ?? null;
    @endphp

    {{-- ✅ غلاف فرعي للـ SubCategory --}}
    @if($subCategory && $subCategory->cover_image)
        <div class="page centered-content">
            <img src="{{ public_path('storage/' . $subCategory->cover_image) }}" style="width:100%;">
        </div>
    @endif

    {{-- ✅ صفحة المنتجات مع الخلفية --}}
    <div class="page">
        @if($productsBg)
            <img src="{{ public_path('storage/' . $productsBg->path) }}" class="background-image">
        @endif

        <div class="content">
            <h2>{{ $subCategory->name ?? 'Other Products' }}</h2>
            <div class="products">
                @foreach($products as $tp)
                    <div class="product">
                        @php
                            $relativePath = str_replace(url('/storage') . '/', '', $tp->image);
                            $absolutePath = public_path('storage/' . $relativePath);
                        @endphp

                        @if($tp->image && file_exists($absolutePath))
                            <img src="file://{{ $absolutePath }}" alt="{{ $tp->name }}">
                        @else
                            <img src="{{ public_path('images/placeholder.jpg') }}" alt="No Image">
                        @endif

                        <p><strong>{{ $tp->name }}</strong></p>
                        <p>{{ $tp->description }}</p>
                        <p><strong>Price:</strong> {{ $tp->price }} EGP</p>
                        <p><strong>Quantity:</strong> {{ $tp->quantity ?? 1 }}</p>
                        <p><strong>Total:</strong> {{ number_format($tp->price * ($tp->quantity ?? 1), 2) }} EGP</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach

{{-- ✅ الغلافات (النهاية) --}}
@foreach($template->endCoverImages as $cover)
    <div class="page centered-content">
        <img src="{{ public_path('storage/' . $cover->path) }}" style="max-width:100%; height:auto;">
    </div>
@endforeach

</body>
</html>
