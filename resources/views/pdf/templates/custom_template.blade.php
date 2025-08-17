<!DOCTYPE html>
<html>
<head>
    <title>Catalog PDF</title>
    <style>
        body {
            font-family: sans-serif;
        }
        h1 {
            color: #333;
        }
        .product {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Catalog for Basket #{{ $basket->id }}</h1>
    <p>Using Template: {{ $template->name }}</p>

    <h3>Products</h3>
    <ul>
        @foreach ($basket->products as $product)
            <li class="product">
                <strong>{{ $product->name }}</strong><br>
                Price: ${{ number_format($product->price, 2) }}<br>
                Description: {{ $product->description ?? 'No description' }}
            </li>
        @endforeach
    </ul>
</body>
</html>
