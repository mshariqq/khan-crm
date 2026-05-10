<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('expense_categories_view')) abort(403);

        $categories = ExpenseCategory::orderBy('name')->paginate(15);
        return view('admin.expense_categories.index', compact('categories'));
    }

    public function create()
    {
        if (!Auth::user()->can('expense_categories_create')) abort(403);
        return view('admin.expense_categories.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('expense_categories_create')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
        ]);

        ExpenseCategory::create($request->only('name'));

        return redirect()->route('admin.expense-categories.index')->with('success', 'Category created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('expense_categories_edit')) abort(403);
        $category = ExpenseCategory::findOrFail($id);
        return view('admin.expense_categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('expense_categories_edit')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $id,
        ]);

        $category = ExpenseCategory::findOrFail($id);
        $category->update($request->only('name'));

        return redirect()->route('admin.expense-categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('expense_categories_delete')) abort(403);
        ExpenseCategory::findOrFail($id)->delete();

        return redirect()->route('admin.expense-categories.index')->with('success', 'Category deleted successfully!');
    }
}
