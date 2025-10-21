<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConsumerProfileRequest;
use App\Http\Resources\ConsumerResource;
use App\Models\Consumer;
use App\Services\ConsumerProfileService;
use App\Traits\Filterable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ConsumerProfileController extends Controller
{
    use Filterable;

    private string $routeName;
    private string $source;
    private Model $model;
    protected ConsumerProfileService $consumerProfileService;

    public function __construct(ConsumerProfileService $consumerProfileService)
    {
        $this->source = 'ConsumerProfile/Pages/';
        $this->routeName = 'profile.consumer.';
        $this->model = new Consumer();
        $this->consumerProfileService = $consumerProfileService;

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}update")->only(['edit', 'update']);
    }

    public function show()
    {
        $user = Auth::user();
        $consumer = $user->consumer;
        $consumer->load([
            'location',
            'phones',
        ]);
        return Inertia::render("{$this->source}Edit", [
            'consumer'  => new ConsumerResource($consumer),
            'title'     => 'Editar Perfil',
            'routeName' => $this->routeName,
        ]);
    }

    public function update(UpdateConsumerProfileRequest $request)
    {
        $consumer = Auth::user()->consumer;
        try {
            $this->consumerProfileService->update($consumer, $request->validated());
            return back()->with('success', 'Perfil actualizado correctamente.');
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), [$exception]);
            return back()->with('error', 'Ha ocurrido un error al actualizar el perfil');
        }
    }
}
