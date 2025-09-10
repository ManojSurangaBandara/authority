<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\MaritalStatusDataTable;
use App\Models\MaritalStatus;

class MaritalStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MaritalStatusDataTable $dataTable)
    {
        return $dataTable->render('marital-statuses.index');
    }

    /**
     * Show the form for creating a new resource.
     * Disabled for view-only feature
     */
    public function create()
    {
        abort(404, 'Creation not allowed for marital statuses');
    }

    /**
     * Store a newly created resource in storage.
     * Disabled for view-only feature
     */
    public function store(Request $request)
    {
        abort(404, 'Creation not allowed for marital statuses');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $maritalStatus = MaritalStatus::findOrFail($id);
        return view('marital-statuses.show', compact('maritalStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     * Disabled for view-only feature
     */
    public function edit(string $id)
    {
        abort(404, 'Editing not allowed for marital statuses');
    }

    /**
     * Update the specified resource in storage.
     * Disabled for view-only feature
     */
    public function update(Request $request, string $id)
    {
        abort(404, 'Updating not allowed for marital statuses');
    }

    /**
     * Remove the specified resource from storage.
     * Disabled for view-only feature
     */
    public function destroy(string $id)
    {
        abort(404, 'Deletion not allowed for marital statuses');
    }
}
