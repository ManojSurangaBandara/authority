<?php

namespace App\Http\Controllers;

use App\DataTables\HandedOverBusPassApplicationDataTable;
use App\DataTables\IntegratedBusPassApplicationDataTable;
use App\DataTables\IntegratedToBuildCardDataTable;
use App\DataTables\NotyetHandedOverBussPassApplicationDataTable;
use App\DataTables\PendingBusPassApplicationDataTable;
use App\DataTables\RejectedBusPassApplicationDataTable;
use App\DataTables\TemporaryCardPrintedDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Establishment;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function rejected(RejectedBusPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        
        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }
        
        return $dataTable->render('reports.rejected-applications', compact('establishments'));
    }

     public function temporary_card_printed(TemporaryCardPrintedDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        
        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }
        
        return $dataTable->render('reports.temporary-card-printed', compact('establishments'));
    }

    public function handed_over(HandedOverBusPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        
        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }
        
        return $dataTable->render('reports.handed-over-applications', compact('establishments'));
    }

     public function not_yet_handed_over(NotyetHandedOverBussPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        
        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }
        
        return $dataTable->render('reports.not-yet-handed-over-applications', compact('establishments'));
    }

     public function integrated(IntegratedBusPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        
        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }
        
        return $dataTable->render('reports.integrated-applications', compact('establishments'));
    }

     public function pending(PendingBusPassApplicationDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        
        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }
        
        return $dataTable->render('reports.pending-applications', compact('establishments'));
    }


       public function build(IntegratedToBuildCardDataTable $dataTable)
    {
        // Filter establishments for branch users
        $user = Auth::user();
        $branchRoles = ['Bus Pass Subject Clerk (Branch)', 'Staff Officer (Branch)', 'Director (Branch)'];
        
        if ($user && $user->hasAnyRole($branchRoles) && $user->establishment_id) {
            $establishments = Establishment::where('id', $user->establishment_id)->get();
        } else {
            $establishments = Establishment::all();
        }
        
        return $dataTable->render('reports.integrated-to-build-card', compact('establishments'));
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
