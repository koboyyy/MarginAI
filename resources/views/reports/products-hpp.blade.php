<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin: 0 0 16px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e5e7eb; text-align: left; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        td.number, th.number { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan HPP Produk</h1>
    <p class="meta">{{ $business->name }} · Dicetak {{ $generatedAt->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th class="number">Yield Batch</th>
                <th class="number">HPP/Unit</th>
                <th class="number">Harga Rekomendasi</th>
                <th class="number">Harga Jual</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->sku ?: '-' }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="number">{{ $product->batch_yield }}</td>
                    <td class="number">{{ $product->calculated_hpp }}</td>
                    <td class="number">{{ $product->recommended_price ?: '-' }}</td>
                    <td class="number">{{ $product->selling_price ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada produk.</td>
                </tr>
            @endempty
        </tbody>
    </table>
</body>
</html>
