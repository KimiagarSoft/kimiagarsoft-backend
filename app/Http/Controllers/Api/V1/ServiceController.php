<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Services\ServiceManagementService;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceManagementService $serviceManagementService
    ) {
    }

    /**
     * Display a paginated listing of services.
     */
    public function index()
    {
        $services = Service::query()
            ->paginate(10);

        return ServiceResource::collection($services);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): ServiceResource
    {
        return new ServiceResource($service);
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request): ServiceResource
    {
        $service = $this->serviceManagementService->create(
            $request->validated()
        );

        return new ServiceResource($service);
    }

    /**
     * Update the specified service.
     */
    public function update(
        UpdateServiceRequest $request,
        Service $service
    ): ServiceResource {
        $service = $this->serviceManagementService->update(
            $service,
            $request->validated()
        );

        return new ServiceResource($service);
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service): JsonResponse
    {
        $this->serviceManagementService->delete($service);

        return response()->json(null, 204);
    }
}

