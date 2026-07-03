<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::orderBy('name')->get();
        return view('features.index', compact('features'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:features,name',
            'is_active' => 'boolean',
        ]);

        Feature::create($validated);

        return redirect()->route('features.index')->with('success', 'Feature added successfully.');
    }

    public function update(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:features,name,' . $feature->id,
            'is_active' => 'boolean',
        ]);

        $feature->update($validated);

        return redirect()->route('features.index')->with('success', 'Feature updated successfully.');
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();

        return redirect()->route('features.index')->with('success', 'Feature deleted successfully.');
    }
}
