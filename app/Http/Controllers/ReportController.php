<?php

namespace App\Http\Controllers;

use App\DataTables\RejectedBusPassApplicationDataTable;
use App\DataTables\TemporaryCardPrintedDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Establishment;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function rejected(RejectedBusPassApplicationDataTable $dataTable)
    {
        $establishments = Establishment::all();
        return $dataTable->render('reports.rejected-applications', compact('establishments'));

    }

     public function temporary_card_printed(TemporaryCardPrintedDataTable $dataTable)
    {
        $establishments = Establishment::all();
        return $dataTable->render('reports.temporary-card-printed', compact('establishments'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
