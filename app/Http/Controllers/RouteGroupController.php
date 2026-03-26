<?php

namespace App\Http\Controllers;

use App\Models\RouteGroup;
use App\Models\BusRoute;
use App\Models\LivingInBuses;
use App\Models\RouteGroupMember;
use Illuminate\Http\Request;

class RouteGroupController extends Controller
{
    public function index()
    {
        $groups = RouteGroup::with(['members'])->orderBy('name')->get();

        $livingOutRoutes = BusRoute::orderBy('name')->get();
        $livingInRoutes = LivingInBuses::orderBy('name')->get();

        return view('route-groups.index', compact('groups', 'livingOutRoutes', 'livingInRoutes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:route_groups,name',
            'members' => 'required|array|min:1',
            'members.*' => 'required|string',
        ]);

        $group = RouteGroup::create([
            'name' => $request->name,
        ]);

        foreach ($request->members as $memberValue) {
            [$routeType, $routeId] = explode(':', $memberValue);
            RouteGroupMember::create([
                'route_group_id' => $group->id,
                'route_type' => $routeType,
                'route_id' => (int)$routeId,
            ]);
        }

        return redirect()->route('route-groups.index')->with('success', 'Route group created successfully.');
    }

    public function update(Request $request, $id)
    {
        $group = RouteGroup::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150|unique:route_groups,name,' . $group->id,
            'members' => 'required|array|min:1',
            'members.*' => 'required|string',
        ]);

        $group->update([
            'name' => $request->name,
        ]);

        // Replace members with current selection (clear then set to avoid stale routes)
        $group->members()->delete();
        foreach ($request->members as $memberValue) {
            [$routeType, $routeId] = explode(':', $memberValue);
            RouteGroupMember::create([
                'route_group_id' => $group->id,
                'route_type' => $routeType,
                'route_id' => (int)$routeId,
            ]);
        }

        return redirect()->route('route-groups.index')->with('success', 'Route group updated successfully.');
    }

    public function destroy($id)
    {
        $group = RouteGroup::findOrFail($id);
        $group->delete();

        return redirect()->route('route-groups.index')->with('success', 'Route group deleted successfully.');
    }
}
