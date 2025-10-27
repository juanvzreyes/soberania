<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCooperativeProfileRequest;
use App\Http\Resources\CooperativeResource;
use App\Models\Cooperative;
use App\Services\CooperativeProfileService;
use App\Traits\Filterable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CooperativeProfileController extends Controller
{
    use Filterable;

    private string $routeName;
    private string $source;
    private Model $model;
    protected CooperativeProfileService $cooperativeProfileService;

    public function __construct(CooperativeProfileService $cooperativeProfileService)
    {
        $this->source = 'CooperativeProfile/Pages/';
        $this->routeName = 'profile.cooperative.';
        $this->model = new Cooperative();
        $this->cooperativeProfileService = $cooperativeProfileService;

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}update")->only(['edit', 'update']);
    }

    public function show()
    {
        $user = Auth::user();
        $cooperative = $user->cooperative;
        if (is_null($cooperative)) {
            $cooperative = Cooperative::create([
                'user_id' => $user->id,
            ]);
        }
        $cooperative->load([
            'location',
            'phones',
        ]);
        return Inertia::render("{$this->source}Edit", [
            'cooperative'  => new CooperativeResource($cooperative),
            'title'     => 'Editar Perfil',
            'routeName' => $this->routeName,
        ]);
    }

    public function update(UpdateCooperativeProfileRequest $request)
    {
        $cooperative = Auth::user()->cooperative;
        try {
            $this->cooperativeProfileService->update($cooperative, $request->validated());
            return back()->with('success', 'Perfil actualizado correctamente.');
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), [$exception]);
            return back()->with('error', 'Ha ocurrido un error al actualizar el perfil');
        }
    }
}
