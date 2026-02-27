<?php

namespace App\Http\Controllers\Colocation;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Services\BalanceService;
use Database\Seeders\CategorySeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ColocationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $colocations = Auth::user()->colocations()->with('owner')->latest()->get();
        return view('colocations.index', compact('colocations'));
    }

    public function create()
    {
        if (Auth::user()->hasActiveColocation()) {
            return back()->withErrors(['error' => 'Vous avez déjà une colocation active.']);
        }

        return view('colocations.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->hasActiveColocation()) {
            return back()->withErrors(['error' => 'Vous avez déjà une colocation active.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $colocation = Colocation::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => Auth::id(),
            'status' => 'active',
        ]);

        $colocation->members()->attach(Auth::id());
        CategorySeeder::seedForColocation($colocation);

        return redirect()->route('colocations.show', $colocation)
            ->with('status', 'Colocation créée avec succès.');
    }

    public function show(Colocation $colocation, BalanceService $balanceService)
    {
        $this->authorize('view', $colocation);
        
        $colocation->load(['owner', 'activeMembers']);
        $balances = $balanceService->calculateBalances($colocation);
        
        return view('colocations.show', compact('colocation', 'balances'));
    }

    public function destroy(Colocation $colocation)
    {
        $this->authorize('delete', $colocation);

        $colocation->update(['status' => 'cancelled']);

        return redirect()->route('dashboard')
            ->with('status', 'Colocation annulée avec succès.');
    }
}
