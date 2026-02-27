<?php

namespace App\Http\Controllers\Colocation;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Colocation $colocation)
    {
        $this->authorize('view', $colocation);

        $validated = $request->validate([
            'from_user_id' => 'required|exists:users,id',
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        Payment::create([
            'colocation_id' => $colocation->id,
            'from_user_id' => $validated['from_user_id'],
            'to_user_id' => $validated['to_user_id'],
            'amount' => $validated['amount'],
            'date' => now(),
        ]);

        return back()->with('status', 'Paiement enregistré avec succès.');
    }

    public function index(Colocation $colocation)
    {
        $this->authorize('view', $colocation);

        $payments = $colocation->payments()->with(['fromUser', 'toUser'])->latest()->get();

        return view('payments.index', compact('colocation', 'payments'));
    }
}
