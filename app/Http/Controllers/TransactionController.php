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

    public function today()
    {
        $currentDate = Carbon::now();
        $transaction = Transaction::with('transaction_details')->whereDate('created_at', $currentDate)->latest()->get();

        return response()->json([
            'transaction' => $transaction
        ], 200);
    }

    public function week()
    {
        $currentDate = Carbon::now();

        // Get the start of the current week (assuming Monday is the first day of the week)
        $startOfWeek = $currentDate->copy()->startOfWeek();

        // Get the end of the current week (assuming Sunday is the last day of the week)
        $endOfWeek = $currentDate->copy()->endOfWeek();

        $transaction = Transaction::with('transaction_details')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->latest()
            ->get();

        return response()->json([
            'transaction' => $transaction,
        ], 200);
    }


    public function month()
    {
        $currentDate = Carbon::now();

        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        $transaction = Transaction::with('transaction_details')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->latest()->get();
        return response()->json([
            'transaction' => $transaction
        ], 200);
    }

    public function year()
    {
        $currentDate = Carbon::now();

        $startOfYear = $currentDate->copy()->startOfYear();
        $endOfYear = $currentDate->copy()->endOfYear();

        $transaction = Transaction::with('transaction_details')->whereBetween('created_at', [$startOfYear, $endOfYear])->latest()->get();
        return response()->json([
            'transaction' => $transaction
        ], 200);
    }

    public function getTransactionsByDate(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;



        // Convert the dates to Carbon instances
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        print("Received startDate: $startDate");
        print("Received endDate: $endDate");

        $startDate = $startDate->toDateTimeString();
        $endDate = $endDate->toDateTimeString();

        // Get the transactions within the date range
        $transactions = Transaction::with('transaction_details')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        print("Number of Transactions: " . count($transactions)); // Add this line

        return response()->json([
            'transactions' => $transactions,
        ], 200);
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
