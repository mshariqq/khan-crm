<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Client;
use Illuminate\Http\Request;

class ContactPaneController extends Controller
{
    public function create(Request $request)
    {
        if (!auth()->user()->can('contacts_create')) {
            abort(403);
        }

        $clients = Client::all();

        return view('admin.contacts.pane.create', compact('clients'));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('contacts_edit')) {
            abort(403);
        }

        $contact = Contact::findOrFail($id);
        $clients = Client::all();

        return view('admin.contacts.pane.edit', compact('contact', 'clients'));
    }

    public function show($id)
    {
        if (!auth()->user()->can('contacts_view')) {
            abort(403);
        }

        $contact = Contact::with('client')->findOrFail($id);

        return view('admin.contacts.pane.show', compact('contact'));
    }
}
