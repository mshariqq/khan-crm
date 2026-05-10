<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('services_view')) {
            abort(403, 'Unauthorized action.');
        }

        $services = Service::paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        if (!Auth::user()->can('services_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('services_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Service::create($request->all());

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('services_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('services_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $service = Service::findOrFail($id);
        $service->update($request->all());

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('services_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully!');
    }
}
