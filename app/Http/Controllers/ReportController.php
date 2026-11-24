<?php

namespace App\Http\Controllers;

use App\Exports\ProductHppExport;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function productsPdf(Request $request): Responsable|Response
    {
        $business = $this->businessFor($request);
        $products = $business->products()->orderBy('name')->get();

        return Pdf::view('reports.products-hpp', [
            'business' => $business,
            'products' => $products,
            'generatedAt' => now(),
        ])
            ->driver('dompdf')
            ->format('a4')
            ->landscape()
            ->name('laporan-hpp-produk.pdf')
            ->download();
    }

    public function productsExcel(Request $request): BinaryFileResponse
    {
        $business = $this->businessFor($request);

        return Excel::download(
            new ProductHppExport($business),
            'laporan-hpp-produk.xlsx',
        );
    }
}
