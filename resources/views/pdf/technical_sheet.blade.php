<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Technical Data Sheet</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.6; font-size: 13px; }
        h1, h2 { color: #2c3e50; margin-bottom: 5px; }
        .section { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        img { max-width: 200px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { padding: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Str;
@endphp


    <h1>📄 Technical Data Sheet</h1>

    <div class="section">
        <h2>🧾 Basic Info</h2>
        <table>
            <tr><th>Name (EN)</th><td>{{ $product->name_en }}</td></tr>
            <tr><th>Name (AR)</th><td>{{ $product->name_ar }}</td></tr>
            <tr><th>SKU</th><td>{{ $product->sku }}</td></tr>
            <tr><th>HS Code</th><td>{{ $product->hs_code }}</td></tr>
            <tr><th>Pack Size</th><td>{{ $product->pack_size }}</td></tr>
            <tr><th>Dimensions</th><td>{{ $product->dimensions }}</td></tr>
            <tr><th>Capacity</th><td>{{ $product->capacity }}</td></tr>
            <tr><th>Specification</th><td>{{ $product->specification }}</td></tr>
            <tr><th>Price</th><td>{{ $product->price }}</td></tr>
            <tr><th>Quantity</th><td>{{ $product->quantity }}</td></tr>
        </table>
    </div>

  
    @if ($product->main_image)
        <div class="section">
            <h2>🖼️ Product Image</h2>
            <img src="{{ public_path('storage/' . Str::after($product->main_image, 'storage/')) }}" alt="Product Image">
        </div>
    @endif

</body>
</html>
