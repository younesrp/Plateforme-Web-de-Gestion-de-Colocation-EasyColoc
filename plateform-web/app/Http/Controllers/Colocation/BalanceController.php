<?php

namespace App\Http\Controllers\Colocation;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Services\BalanceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BalanceController extends Controller
{
    use AuthorizesRequests;

    public function show(Colocation $colocation, BalanceService $balanceService)
    {
        $this->authorize('view', $colocation);

        $balances = $balanceService->calculateBalances($colocation);

        return view('balances.show', compact('colocation', 'balances'));
    }
}
