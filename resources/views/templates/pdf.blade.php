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
            height: 100vh;
            overflow: hidden;
        }
        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            justify-content: center;
            padding: 10px;
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
            max-height: 200px;
            object-fit: contain;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

{{-- ✅ الغلافات (البداية) --}}
@foreach($template->startCoverImages as $cover)
    @if($cover->background_position !== 'products' && $cover->background_position !== 'client')
        <div class="page centered-content">
            <img src="{{ public_path('storage/' . $cover->path) }}" style="max-width:100%; height:auto;">
        </div>
    @endif
@endforeach

{{-- ✅ المنتجات حسب الـ SubCategory --}}
@php
    $productsBg = $template->coverImages->where('background_position', 'products')->first();
@endphp

@foreach($groupedProducts as $subCategoryId => $products)
    @php
        $subCategory = $products->first()->product->subCategory ?? null;
        $chunkedProducts = $products->chunk(4); // كل صفحة فيها ٤ منتجات
    @endphp

    {{-- ✅ غلاف الـ SubCategory --}}
    @if($subCategory && $subCategory->cover_image)
        <div class="page centered-content">
            @if($template->logo)
                <img src="{{ public_path('storage/' . $template->logo) }}" class="logo-top-right">
            @endif
            <img src="{{ public_path('storage/' . $subCategory->cover_image) }}" style="width:100%;">
        </div>
    @endif

    {{-- ✅ صفحات المنتجات --}}
    @foreach($chunkedProducts as $chunk)
        <div class="page">
            @if($productsBg)
                <img src="{{ public_path('storage/' . $productsBg->path) }}" class="background-image">
            @endif

            <div class="content">
                @if($template->logo)
                    <img src="{{ public_path('storage/' . $template->logo) }}" class="logo-top-right">
                @endif

                <h2>{{ $subCategory->name ?? 'Other Products' }}</h2>

                <div class="products">
                    @foreach($chunk as $tp)
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
        <!-- <h1>{{ $template->name }}</h1> -->
        @if($template->logo)
            <img src="{{ public_path('storage/'.$template->logo) }}" class="logo-top-right">
        @endif

        <h2>Client Information</h2>
        <p> {{ $client->client_name ?? 'N/A' }}</p>
        <p> {{ $client->email ?? 'N/A' }}</p>
        <p> {{ $client->phone ?? 'N/A' }}</p>

        <h2>Created By</h2>
        <p> {{ $user->name }}</p>
        <p>{{ $user->email }}</p>
        <p> {{ $template->created_at->format('d M Y') }}</p>

        <hr>

        <h2>Product Summary</h2>
        <table border="1" cellpadding="10" cellspacing="0" width="100%" style="border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th>Sub Category</th>
                    <th>Product Count</th>
                    <th>Sub Total (EGP)</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPrice = 0; @endphp

                @foreach($groupedProducts as $subCategoryId => $products)
                    @php
                        $subCategory = $products->first()->product->subCategory ?? null;
                        $subCategoryName = $subCategory ? $subCategory->name : 'Other';
                        $count = $products->count();
                        $subTotal = $products->sum(function($item) {
                            return $item->price * ($item->quantity ?? 1);
                        });
                        $totalPrice += $subTotal;
                    @endphp

                    <tr>
                        <td><strong>{{ $subCategoryName }}</strong></td>
                        <td>{{ $count }}</td>
                        <td>{{ number_format($subTotal, 2) }} EGP</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding-left: 20px;">
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach($products as $p)
                                    <li>{{ $p->name }} - {{ $p->quantity ?? 1 }} × {{ number_format($p->price, 2) }} EGP = {{ number_format($p->price * ($p->quantity ?? 1), 2) }} EGP</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" align="right"><strong>Total Price:</strong></td>
                    <td><strong>{{ number_format($totalPrice, 2) }} EGP</strong></td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>

{{-- ✅ الغلافات (النهاية) --}}
@foreach($template->endCoverImages as $cover)
    <div class="page centered-content">
        <img src="{{ public_path('storage/' . $cover->path) }}" style="max-width:100%; height:auto;">
    </div>
@endforeach

</body>
</html>
