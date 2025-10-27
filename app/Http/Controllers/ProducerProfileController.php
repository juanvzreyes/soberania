<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProducerProfileRequest;
use App\Http\Resources\ProducerResource;
use App\Models\Producer;
use App\Services\ProducerProfileService;
use App\Traits\Filterable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProducerProfileController extends Controller
{
    use Filterable;

    private string $routeName;
    private string $source;
    private Model $model;
    protected ProducerProfileService $producerProfileService;

    public function __construct(ProducerProfileService $producerProfileService)
    {
        $this->source = 'ProducerProfile/Pages/';
        $this->routeName = 'profile.producer.';
        $this->model = new Producer();
        $this->producerProfileService = $producerProfileService;

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}update")->only(['edit', 'update']);
    }

    public function show()
    {
        $user = Auth::user();
        $producer = $user->producer;
        if (is_null($producer)) {
            $producer = Producer::create([
                'user_id' => $user->id,
            ]);
        }
        $producer->load([
            'location',
            'phones',
        ]);
        return Inertia::render("{$this->source}Edit", [
            'producer'  => new ProducerResource($producer),
            'title'     => 'Editar Perfil',
            'routeName' => $this->routeName,
        ]);
    }

    public function update(UpdateProducerProfileRequest $request)
    {
        $producer = Auth::user()->producer;
        try {
            $this->producerProfileService->update($producer, $request->validated());
            return back()->with('success', 'Perfil actualizado correctamente.');
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), [$exception]);
            return back()->with('error', 'Ha ocurrido un error al actualizar el perfil');
        }
    }
}
