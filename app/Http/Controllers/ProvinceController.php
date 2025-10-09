<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;
use App\DataTables\ProvinceDataTable;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProvinceDataTable $dataTable)
    {
        return $dataTable->render('province.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('province.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|unique:provinces,name',
        ]);

        Province::create($request->all());

        return redirect()->route('province.index')
            ->with('success', 'Province created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $province = Province::findOrFail($id);
        return view('province.show', compact('province'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $province = Province::findOrFail($id);
        return view('province.edit', compact('province'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:50|unique:provinces,name,'.$id,
        ]);
        $province = Province::findOrFail($id);
        $province->update($request->all());
        return redirect()->route('province.index')
            ->with('success', 'Province updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $province = Province::findOrFail($id);
        $province->delete();
        return redirect()->route('province.index')
            ->with('success', 'Province deleted successfully.');
    }

    












}
