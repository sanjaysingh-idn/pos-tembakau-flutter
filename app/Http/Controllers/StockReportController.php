<?php

namespace App\Http\Controllers;

use App\Models\StockReport;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index()
    {
        $stock = StockReport::with('product')->get();
        return response([
            'stock' => $stock
        ], 200);
    }
}
