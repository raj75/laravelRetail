<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $query = Party::query()->orderBy('name');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")
                ->orWhere('gstin', 'like', "%{$s}%"));
        }

        $parties = $query->paginate(20);

        return view('parties.index', compact('parties'));
    }

    public function create()
    {
        return view('parties.form', ['party' => new Party]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['current_balance'] = $data['opening_balance'];
        Party::create($data);

        return redirect()->route('parties.index')->with('success', 'Party created.');
    }

    public function show(Party $party)
    {
        $party->load(['invoices' => fn ($q) => $q->latest()->limit(20), 'payments' => fn ($q) => $q->latest()->limit(10)]);

        return view('parties.show', compact('party'));
    }

    public function edit(Party $party)
    {
        return view('parties.form', compact('party'));
    }

    public function update(Request $request, Party $party)
    {
        $party->update($this->validated($request));

        return redirect()->route('parties.index')->with('success', 'Party updated.');
    }

    public function destroy(Party $party)
    {
        $party->delete();

        return redirect()->route('parties.index')->with('success', 'Party deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:customer,supplier,both'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'gstin' => ['nullable', 'string', 'max:15'],
            'pan' => ['nullable', 'string', 'max:10'],
            'billing_address' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'balance_type' => ['nullable', 'in:receive,pay'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
