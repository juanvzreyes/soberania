<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Municipality;
use App\Models\Neighborhood;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get all states.
     */
    public function getStates(): JsonResponse
    {
        $states = State::orderBy('name')->get();
        return response()->json($states);
    }

    /**
     * Get neighborhoods by municipality ID.
     */
    public function getNeighborhoods(Municipality $municipality, Request $request): JsonResponse
    {
        $query = $municipality->neighborhoods()->orderBy('name');
        
        if ($request->has('postal_code')) {
            $query->where('postal_code', $request->input('postal_code'));
        }

        $neighborhoods = $query->get();
        return response()->json($neighborhoods);
    }

    /**
     * Get municipalities by state ID.
     */
    public function getMunicipalities(State $state): JsonResponse
    {
        $municipalities = $state->municipalities()->orderBy('name')->get();
        return response()->json($municipalities);
    }

    /**
     * Get neighborhood, municipality, and state by postal code.
     */
    public function getPostalCode(string $postalCode): JsonResponse
    {
        $neighborhood = Neighborhood::where('postal_code', $postalCode)
            ->with('municipality.state')
            ->first();

        if (!$neighborhood) {
            return response()->json(['message' => 'Código postal no encontrado.'], 404);
        }

        return response()->json([
            'neighborhood_id' => $neighborhood->id,
            'neighborhood_name' => $neighborhood->name,
            'municipality_id' => $neighborhood->municipality->id,
            'municipality_name' => $neighborhood->municipality->name,
            'state_id' => $neighborhood->municipality->state->id,
            'state_name' => $neighborhood->municipality->state->name,
        ]);
    }
}
