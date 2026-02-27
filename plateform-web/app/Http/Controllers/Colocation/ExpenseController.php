<?php

namespace App\Http\Controllers\Colocation;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Colocation $colocation)
    {
        $this->authorize('view', $colocation);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'payer_id' => Auth::id(),
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('status', 'Dépense ajoutée avec succès.');
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        if ($expense->payer_id !== Auth::id() && !$colocation->isOwner(Auth::user())) {
            abort(403);
        }

        $expense->delete();

        return back()->with('status', 'Dépense supprimée avec succès.');
    }
}
