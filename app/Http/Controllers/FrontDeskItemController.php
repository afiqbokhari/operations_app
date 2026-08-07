<?php

namespace App\Http\Controllers;

use App\Models\FrontDeskItem;
use App\Models\FrontDeskMatter;
use App\Models\FrontDeskContact;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class FrontDeskItemController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('legal')) {
            return redirect()->route('front-desk.mail.legal.index');
        }

        $todayCount = FrontDeskItem::today()->count();
        $pendingPass = FrontDeskItem::whereNull('passed_to')->count();
        $awaitingCollection = FrontDeskItem::whereNotNull('passed_to')->whereNull('collected_by')->count();

        $items = FrontDeskItem::with(['contact', 'matter', 'passedTo', 'passedBy', 'collectedBy', 'loggedBy'])
            ->when($request->search, function ($query, $search) {
                $query->where('address_to', 'like', "%{$search}%")
                    ->orWhereHas('contact', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('matter', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                match ($status) {
                    'pending' => $query->whereNull('passed_to'),
                    'passed' => $query->whereNotNull('passed_to')->whereNull('collected_by'),
                    'collected' => $query->whereNotNull('collected_by'),
                    default => null,
                };
            })
            ->when($request->received_via, function ($query, $via) {
                $query->where('received_via', $via);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('date_received', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('date_received', '<=', $date);
            })
            ->orderBy('date_received', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $legalUsers = User::role('legal')->orderBy('name')->get();

        return view('front-desk.index', compact('items', 'todayCount', 'pendingPass', 'awaitingCollection', 'legalUsers'));
    }

    public function dashboard()
    {
        $todayCount = FrontDeskItem::today()->count();
        $pendingPickups = FrontDeskItem::pendingPickup()->count();
        $agingItems = FrontDeskItem::aging()->count();
        $monthCount = FrontDeskItem::whereBetween('date_received', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $recentItems = FrontDeskItem::with(['contact', 'matter', 'collectedBy', 'loggedBy'])
            ->orderBy('date_received', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('front-desk.dashboard', compact('todayCount', 'pendingPickups', 'agingItems', 'monthCount', 'recentItems'));
    }

    public function create()
    {
        $matters = FrontDeskMatter::select('id', 'name')->orderBy('name')->get();
        $contacts = FrontDeskContact::select('id', 'name', 'company')->orderBy('name')->get();
        $legalUsers = User::role('legal')->orderBy('name')->get();

        return view('front-desk.create', compact('matters', 'contacts', 'legalUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_received' => 'required|date',
            'batch_number' => 'nullable|integer|min:1|max:5',
            'contact_name' => 'required|string|max:255',
            'address_to' => 'required|string|max:255',
            'passed_to' => 'nullable|exists:users,id',
            'letter_date' => 'nullable|date',
            'case_reference' => 'nullable|string|max:20',
            'matter_name' => 'nullable|string|max:255',
            'received_via' => 'required|in:Hand Delivery,Courier,Post',
            'doc_type' => 'required|array',
            'doc_type.*' => 'string|max:100',
            'details' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // Find or create contact
        $contact = FrontDeskContact::firstOrCreate(
            ['name' => $validated['contact_name']]
        );
        $validated['contact_id'] = $contact->id;
        unset($validated['contact_name']);

        // Find or create matter (if provided)
        if (!empty($validated['matter_name'])) {
            $matter = FrontDeskMatter::firstOrCreate(
                ['name' => $validated['matter_name']]
            );
            $validated['matter_id'] = $matter->id;
        }
        unset($validated['matter_name']);

        if (!empty($validated['passed_to'])) {
            $validated['passed_by'] = auth()->id();
            $validated['passed_at'] = now();
        }

        $validated['doc_type'] = array_values(array_filter($validated['doc_type'], function($value) {
            return !empty(trim($value));
        }));
        $validated['logged_by'] = auth()->id();

        $item = FrontDeskItem::create($validated);

        ActivityLogger::log('FrontDeskItem', $item->id, 'created', [
            'Received from: ' . $contact->name,
            'To: ' . $validated['address_to'],
            'Via: ' . $validated['received_via'],
        ]);

        return redirect()->route('front-desk.mail.index')
            ->with('success', 'Mail/package logged successfully.');
    }

    public function show(FrontDeskItem $frontDeskItem)
    {
        $frontDeskItem->load(['contact', 'matter', 'collectedBy', 'loggedBy', 'passedTo', 'passedBy']);
        $legalUsers = User::role('legal')->orderBy('name')->get();
        
        return view('front-desk.show', compact('frontDeskItem', 'legalUsers'));
    }

    public function edit(FrontDeskItem $frontDeskItem)
    {
        $matters = FrontDeskMatter::select('id', 'name')->orderBy('name')->get();
        $contacts = FrontDeskContact::select('id', 'name', 'company')->orderBy('name')->get();
        $legalUsers = User::role('legal')->orderBy('name')->get();
        $frontDeskItem->load(['contact', 'matter', 'collectedBy', 'loggedBy', 'passedTo', 'passedBy']);

        return view('front-desk.edit', compact('frontDeskItem', 'matters', 'contacts', 'legalUsers'));
    }

    public function update(Request $request, FrontDeskItem $frontDeskItem)
    {
        $validated = $request->validate([
            'date_received' => 'required|date',
            'batch_number' => 'nullable|integer|min:1|max:5',
            'contact_name' => 'required|string|max:255',
            'address_to' => 'required|string|max:255',
            'passed_to' => 'nullable|exists:users,id',
            'letter_date' => 'nullable|date',
            'case_reference' => 'nullable|string|max:20',
            'matter_name' => 'nullable|string|max:255',
            'received_via' => 'required|in:Hand Delivery,Courier,Post',
            'doc_type' => 'required|array',
            'doc_type.*' => 'string|max:100',
            'details' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // Find or create contact
        $contact = FrontDeskContact::firstOrCreate(
            ['name' => $validated['contact_name']]
        );
        $validated['contact_id'] = $contact->id;
        unset($validated['contact_name']);

        // Find or create matter (if provided)
        if (!empty($validated['matter_name'])) {
            $matter = FrontDeskMatter::firstOrCreate(
                ['name' => $validated['matter_name']]
            );
            $validated['matter_id'] = $matter->id;
        } else {
            $validated['matter_id'] = null;
        }
        unset($validated['matter_name']);

        if (!empty($validated['passed_to'])) {
            $validated['passed_by'] = auth()->id();
            $validated['passed_at'] = now();
        } else {
            $validated['passed_by'] = null;
            $validated['passed_at'] = null;
        }

        $validated['doc_type'] = array_values(array_filter($validated['doc_type'], function($value) {
            return !empty(trim($value));
        }));

        $frontDeskItem->update($validated);

        ActivityLogger::log('FrontDeskItem', $frontDeskItem->id, 'updated', [
            'Updated mail/package details',
        ]);

        return redirect()->route('front-desk.mail.index')
            ->with('success', 'Mail/package updated successfully.');
    }

    public function destroy(FrontDeskItem $frontDeskItem)
    {
        $frontDeskItem->delete();

        ActivityLogger::log('FrontDeskItem', $frontDeskItem->id, 'deleted', [
            'Deleted mail/package',
        ]);

        return redirect()->route('front-desk.mail.index')
            ->with('success', 'Mail/package deleted.');
    }

    public function pass(Request $request, FrontDeskItem $frontDeskItem)
    {
        $validated = $request->validate([
            'passed_to' => 'required|exists:users,id',
        ]);

        $frontDeskItem->update([
            'passed_to' => $validated['passed_to'],
            'passed_by' => auth()->id(),
            'passed_at' => now(),
        ]);

        ActivityLogger::log('FrontDeskItem', $frontDeskItem->id, 'passed', [
            'Passed to legal: ' . $frontDeskItem->contact?->name,
        ]);

        return back()->with('success', 'Item passed to legal.');
    }

    public function batchPass(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:front_desk_items,id',
            'passed_to' => 'required|exists:users,id',
        ]);

        $count = FrontDeskItem::whereIn('id', $validated['items'])
            ->whereNull('passed_to')
            ->update([
                'passed_to' => $validated['passed_to'],
                'passed_by' => auth()->id(),
                'passed_at' => now(),
            ]);

        if ($count > 0) {
            ActivityLogger::log('FrontDeskItem', 0, 'batch_passed', [
                "Batch passed to legal: {$count} items",
            ]);
        }

        return redirect()->route('front-desk.mail.index')
            ->with('success', "{$count} item(s) passed to legal.");
    }

    public function undoPass(FrontDeskItem $frontDeskItem)
    {
        if ($frontDeskItem->collected_by) {
            return back()->with('error', 'Cannot undo - item already collected by legal.');
        }

        $frontDeskItem->update([
            'passed_to' => null,
            'passed_by' => null,
            'passed_at' => null,
        ]);

        ActivityLogger::log('FrontDeskItem', $frontDeskItem->id, 'undo_pass', [
            'Undo pass: ' . $frontDeskItem->contact?->name,
        ]);

        return back()->with('success', 'Pass undone. Item returned to pending.');
    }

    public function legalIndex(Request $request)
    {
        $items = FrontDeskItem::with(['contact', 'matter', 'passedTo', 'passedBy', 'collectedBy'])
            ->when($request->search, function ($query, $search) {
                $query->where('address_to', 'like', "%{$search}%")
                    ->orWhereHas('contact', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('matter', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'collected') {
                    $query->whereNotNull('collected_by');
                } elseif ($status === 'passed') {
                    $query->whereNull('collected_by');
                }
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('date_received', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('date_received', '<=', $date);
            })
            ->orderBy('date_received', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('front-desk.legal-index', compact('items'));
    }

    public function legalCollect(FrontDeskItem $frontDeskItem)
    {
        if ($frontDeskItem->collected_by) {
            return back()->with('error', 'This item has already been collected.');
        }

        $frontDeskItem->update([
            'collected_by' => auth()->id(),
            'collected_at' => now(),
        ]);

        ActivityLogger::log('FrontDeskItem', $frontDeskItem->id, 'collected', [
            'Collected by legal: ' . $frontDeskItem->contact?->name,
        ]);

        return back()->with('success', 'Item marked as collected.');
    }

    public function legalBatchCollect(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:front_desk_items,id',
        ]);

        $count = FrontDeskItem::whereIn('id', $validated['items'])
            ->whereNull('collected_by')
            ->update([
                'collected_by' => auth()->id(),
                'collected_at' => now(),
            ]);

        if ($count > 0) {
            ActivityLogger::log('FrontDeskItem', 0, 'legal_batch_collected', [
                "Legal batch pickup: {$count} items",
            ]);
        }

        return redirect()->route('front-desk.mail.legal.index')
            ->with('success', "{$count} item(s) collected.");
    }
}