<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BankApiService;

class BankApiController extends Controller
{
    protected $bankApiService;

    public function __construct(BankApiService $bankApiService)
    {
        $this->bankApiService = $bankApiService;
    }

    public function inquiry(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|string',
            'account_number' => 'required|string',
        ]);

        $result = $this->bankApiService->inquiryAccount(
            $request->input('bank_code'),
            $request->input('account_number')
        );

        if ($result) {
            return response()->json($result);
        } else {
            return response()->json(['message' => 'Gagal melakukan inquiry'], 500);
        }
    }
}
