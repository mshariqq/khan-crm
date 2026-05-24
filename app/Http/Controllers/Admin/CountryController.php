<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountryController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('countries_view')) {
            abort(403, 'Unauthorized action.');
        }

        $countries = Country::paginate(15);
        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        if (!Auth::user()->can('countries_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.countries.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('countries_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'iso2' => 'required|string|max:2',
            'short_name' => 'required|string|max:255',
            'long_name' => 'required|string|max:255',
            'iso3' => 'nullable|string|max:3',
            'numcode' => 'nullable|string|max:10',
            'un_member' => 'nullable|boolean',
            'calling_code' => 'nullable|string|max:20',
            'cctld' => 'nullable|string|max:10',
        ]);

        Country::create($request->only(['iso2', 'short_name', 'long_name', 'iso3', 'numcode', 'un_member', 'calling_code', 'cctld']));

        return redirect()->route('admin.countries.index')->with('success', 'Country created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('countries_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $country = Country::findOrFail($id);
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('countries_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'iso2' => 'required|string|max:2',
            'short_name' => 'required|string|max:255',
            'long_name' => 'required|string|max:255',
            'iso3' => 'nullable|string|max:3',
            'numcode' => 'nullable|string|max:10',
            'un_member' => 'nullable|boolean',
            'calling_code' => 'nullable|string|max:20',
            'cctld' => 'nullable|string|max:10',
        ]);

        $country = Country::findOrFail($id);
        $country->update($request->only(['iso2', 'short_name', 'long_name', 'iso3', 'numcode', 'un_member', 'calling_code', 'cctld']));

        return redirect()->route('admin.countries.index')->with('success', 'Country updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('countries_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $country = Country::findOrFail($id);
        $country->delete();

        return redirect()->route('admin.countries.index')->with('success', 'Country deleted successfully!');
    }
}
