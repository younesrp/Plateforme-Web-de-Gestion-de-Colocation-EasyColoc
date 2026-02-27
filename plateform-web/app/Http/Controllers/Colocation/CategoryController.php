<?php

namespace App\Http\Controllers\Colocation;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Colocation $colocation)
    {
        $this->authorize('view', $colocation);
        $categories = $colocation->categories;
        return view('categories.manage', compact('colocation', 'categories'));
    }

    public function store(Request $request, Colocation $colocation)
    {
        $this->authorize('update', $colocation);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        Category::create([
            'colocation_id' => $colocation->id,
            'name' => $validated['name'],
            'color' => $validated['color'],
        ]);

        return back()->with('status', 'Catégorie créée avec succès.');
    }

    public function destroy(Colocation $colocation, Category $category)
    {
        $this->authorize('update', $colocation);

        if ($category->colocation_id !== $colocation->id) {
            abort(403);
        }

        $category->delete();

        return back()->with('status', 'Catégorie supprimée avec succès.');
    }
}
