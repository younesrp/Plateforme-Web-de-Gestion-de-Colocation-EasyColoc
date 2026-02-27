<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $totalUsers = User::count();
        $totalColocations = Colocation::count();
        $totalExpenses = Expense::sum('amount');
        $users = User::orderBy('created_at', 'desc')->get();
        $bannedUsers = User::where('is_banned', true)->get();

        return view('admin.stats', compact('totalUsers', 'totalColocations', 'totalExpenses', 'users', 'bannedUsers'));
    }

    public function users()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.users', compact('users'));
    }

    public function banUser(User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $user->update(['is_banned' => true]);

        return back()->with('status', 'Utilisateur banni avec succès.');
    }

    public function unbanUser(User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $user->update(['is_banned' => false]);

        return back()->with('status', 'Utilisateur débanni avec succès.');
    }
}
