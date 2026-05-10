<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('contacts_view')) {
            abort(403, 'Unauthorized action.');
        }

        $contacts = Contact::with('client')->paginate(15);
        return view('admin.contacts.index', compact('contacts'));
    }

    public function create()
    {
        if (!Auth::user()->can('contacts_create')) {
            abort(403, 'Unauthorized action.');
        }

        $clients = Client::all();

        return view('admin.contacts.create', compact('clients'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('contacts_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'userid' => 'required|exists:tblclients,userid',
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'email' => 'required|email|max:100|unique:tblcontacts,email',
            'phonenumber' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
            'active' => 'nullable',
            'is_primary' => 'nullable',
        ]);

        $maxId = DB::table('tblcontacts')->max('id') ?? 0;
        $contactId = $maxId + 1;

        Contact::create([
            'id' => $contactId,
            'userid' => $request->userid,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber ?? '',
            'title' => $request->title,
            'password' => bcrypt($request->password),
            'datecreated' => now(),
            'active' => $request->boolean('active', true),
            'is_primary' => $request->boolean('is_primary', true),
        ]);

        return redirect()->route('admin.contacts.index')->with('success', 'Contact created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('contacts_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $contact = Contact::findOrFail($id);
        $clients = Client::all();

        return view('admin.contacts.edit', compact('contact', 'clients'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('contacts_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'userid' => 'required|exists:tblclients,userid',
            'firstname' => 'required|string|max:191',
            'lastname' => 'required|string|max:191',
            'email' => 'required|email|max:100|unique:tblcontacts,email,' . $id,
            'phonenumber' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
            'active' => 'nullable',
            'is_primary' => 'nullable',
        ]);

        $contact = Contact::findOrFail($id);
        $data = [
            'userid' => $request->userid,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber ?? '',
            'title' => $request->title,
            'active' => $request->boolean('active', true),
            'is_primary' => $request->boolean('is_primary', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $contact->update($data);

        return redirect()->route('admin.contacts.index')->with('success', 'Contact updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('contacts_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Contact deleted successfully!');
    }
}
