<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index()
    {
        $transaction = Transaction::with('transaction_details')->latest()->get();
        return response()->json([
            'transaction' => $transaction
        ], 200);
    }

    public function getTotal()
    {
        $currentDate = Carbon::now();

        // Total and total transactions for today
        $totalToday = Transaction::whereDate('created_at', $currentDate)
            ->sum('final_price');
        $totalTodayTransaction = Transaction::whereDate('created_at', $currentDate)->count();

        // Total and total transactions for this month
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();
        $totalThisMonth = Transaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('final_price');
        $totalThisMonthTransaction = Transaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // Total and total transactions for this year
        $startOfYear = $currentDate->copy()->startOfYear();
        $endOfYear = $currentDate->copy()->endOfYear();
        $totalThisYear = Transaction::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->sum('final_price');
        $totalThisYearTransaction = Transaction::whereBetween('created_at', [$startOfYear, $endOfYear])->count();

        return response()->json([
            'totalToday' => $totalToday,
            'totalTodayTransaction' => $totalTodayTransaction,
            'totalThisMonth' => $totalThisMonth,
            'totalThisMonthTransaction' => $totalThisMonthTransaction,
            'totalThisYear' => $totalThisYear,
            'totalThisYearTransaction' => $totalThisYearTransaction,
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    public function store(Request $request)
    {
        $invoice_id = $this->generateInvoiceId();

        // Create the main transaction
        $transaction = Transaction::create([
            'invoice_id'    => $invoice_id,
            'name'          => $request->name,
            'total_items'   => $request->total_items,
            'total_price'   => $request->total_price,
            'discount'      => $request->discount,
            'final_price'   => $request->final_price,
            'cash'          => $request->cash,
            'change'        => $request->change,
        ]);

        // Decode the JSON data for transaction_details
        $transaction_details = json_decode($request->transaction_details, true);

        foreach ($transaction_details as $detail) {
            // Assuming $detail is an array with keys 'product_name', 'product_price', and 'qty'
            DB::table('transaction_details')->insert([
                'transaction_id' => $transaction->id,
                'product_name'   => $detail['product_name'],
                'product_price'  => $detail['product_price'],
                'qty'            => $detail['qty'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return response()->json([
            'transaction' => $transaction,
            'message' => 'Transaksi berhasil diproses',
        ], 200);
    }


    private function generateInvoiceId()
    {
        $latest_invoice = Transaction::latest('created_at')->first();

        if ($latest_invoice) {
            $latest_invoice_date = $latest_invoice->created_at;
            if ($latest_invoice_date->isToday()) {
                $last_invoice_number = intval(substr($latest_invoice->invoice_id, -3));
            } else {
                $last_invoice_number = 0;
            }
        } else {
            $last_invoice_number = 0;
        }

        $current_date = now();
        $invoice_number = str_pad($last_invoice_number + 1, 3, '0', STR_PAD_LEFT);
        $invoice_id = $current_date->format('ymd') . $invoice_number;

        return $invoice_id;
    }
}
