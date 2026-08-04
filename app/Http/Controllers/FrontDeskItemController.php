<?php

namespace App\Http\Controllers;

use App\Models\FrontDeskItem;
use App\Models\FrontDeskMatter;
use App\Models\FrontDeskContact;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class FrontDeskItemController extends Controller
{
    public function index(Request $request)
    {
        $todayCount = FrontDeskItem::today()->count();
        $pendingPickups = FrontDeskItem::pendingPickup()->count();
        $agingItems = FrontDeskItem::aging()->count();

        $items = FrontDeskItem::with(['matter', 'collectedBy', 'loggedBy'])
            ->when($request->search, function ($query, $search) {
                $query->where('received_from', 'like', "%{$search}%")
                    ->orWhere('address_to', 'like', "%{$search}%")
                    ->orWhere('batch_name', 'like', "%{$search}%")
                    ->orWhereHas('matter', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                match ($status) {
                    'pending' => $query->pendingPickup(),
                    'collected' => $query->whereNotNull('collected_by'),
                    'aging' => $query->aging(),
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

        return view('front-desk.index', compact('items', 'todayCount', 'pendingPickups', 'agingItems'));
    }

    public function dashboard()
    {
        $todayCount = FrontDeskItem::today()->count();
        $pendingPickups = FrontDeskItem::pendingPickup()->count();
        $agingItems = FrontDeskItem::aging()->count();
        $monthCount = FrontDeskItem::whereBetween('date_received', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $recentItems = FrontDeskItem::with(['matter', 'collectedBy', 'loggedBy'])
            ->orderBy('date_received', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('front-desk.dashboard', compact('todayCount', 'pendingPickups', 'agingItems', 'monthCount', 'recentItems'));
    }

    public function create()
    {
        $matters = FrontDeskMatter::orderBy('name')->get();
        $contacts = FrontDeskContact::orderBy('name')->get();

        return view('front-desk.create', compact('matters', 'contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_received' => 'required|date',
            'received_from' => 'required|string|max:255',
            'address_to' => 'required|string|max:255',
            'letter_date' => 'nullable|date',
            'matter_id' => 'nullable|exists:front_desk_matters,id',
            'received_via' => 'required|in:Hand Delivery,Courier,Post',
            'doc_type' => 'required|array',
            'doc_type.*' => 'string|max:100',
            'details' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['doc_type'] = array_values(array_filter($validated['doc_type'], function($value) {
            return !empty(trim($value));
        }));
        $validated['logged_by'] = auth()->id();

        $item = FrontDeskItem::create($validated);

        ActivityLogger::log('FrontDeskItem', $item->id, 'created', [
            'Received: ' . $validated['received_from'],
            'To: ' . $validated['address_to'],
            'Via: ' . $validated['received_via'],
        ]);

        return redirect()->route('front-desk.mail.index')
            ->with('success', 'Mail/package logged successfully.');
    }

    public function show(FrontDeskItem $frontDeskItem)
    {
        $frontDeskItem->load(['matter', 'collectedBy', 'loggedBy']);

        return view('front-desk.show', compact('frontDeskItem'));
    }

    public function edit(FrontDeskItem $frontDeskItem)
    {
        $matters = FrontDeskMatter::orderBy('name')->get();
        $contacts = FrontDeskContact::orderBy('name')->get();
        $frontDeskItem->load(['matter', 'collectedBy', 'loggedBy']);

        return view('front-desk.edit', compact('frontDeskItem', 'matters', 'contacts'));
    }

    public function update(Request $request, FrontDeskItem $frontDeskItem)
    {
        $validated = $request->validate([
            'date_received' => 'required|date',
            'received_from' => 'required|string|max:255',
            'address_to' => 'required|string|max:255',
            'letter_date' => 'nullable|date',
            'matter_id' => 'nullable|exists:front_desk_matters,id',
            'received_via' => 'required|in:Hand Delivery,Courier,Post',
            'doc_type' => 'required|array',
            'doc_type.*' => 'string|max:100',
            'details' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

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

    public function batchCollect(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:front_desk_items,id',
        ]);

        $batchName = 'BATCH-' . now()->format('Ymd-His');
        $count = 0;

        $items = FrontDeskItem::whereIn('id', $validated['items'])
            ->whereNull('collected_by')
            ->get();

        foreach ($items as $item) {
            $item->update([
                'batch_name' => $batchName,
                'collected_by' => auth()->id(),
                'collected_at' => now(),
            ]);
            $count++;
        }

        if ($count > 0) {
            ActivityLogger::log('FrontDeskItem', 0, 'batch_collected', [
                "Batch pickup: {$count} items",
                "Batch: {$batchName}",
            ]);
        }

        return redirect()->route('front-desk.mail.index')
            ->with('success', "{$count} item(s) collected in batch {$batchName}.");
    }

    public function collect(FrontDeskItem $frontDeskItem)
    {
        if ($frontDeskItem->collected_by) {
            return back()->with('error', 'This item has already been collected.');
        }

        $frontDeskItem->update([
            'collected_by' => auth()->id(),
            'collected_at' => now(),
        ]);

        ActivityLogger::log('FrontDeskItem', $frontDeskItem->id, 'collected', [
            'Collected: ' . $frontDeskItem->received_from,
            'To: ' . $frontDeskItem->address_to,
        ]);

        return back()->with('success', 'Item marked as collected.');
    }

    public function undoCollect(FrontDeskItem $frontDeskItem)
    {
        if (!$frontDeskItem->collected_by) {
            return back()->with('error', 'This item has not been collected yet.');
        }

        $frontDeskItem->update([
            'collected_by' => null,
            'collected_at' => null,
            'batch_name' => null,
        ]);

        ActivityLogger::log('FrontDeskItem', $frontDeskItem->id, 'undo_collected', [
            'Undo collection: ' . $frontDeskItem->received_from,
            'To: ' . $frontDeskItem->address_to,
        ]);

        return back()->with('success', 'Collection undone. Item is pending again.');
    }
}