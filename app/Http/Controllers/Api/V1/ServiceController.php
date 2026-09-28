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
    ) {}

    /**
     * Display a searched, filtered, sorted, and paginated listing of services.
     */
    public function index()
    {
        $query = Service::query();

        if (request()->filled('search')) {
            $search = request('search');

            $query->where(function ($query) use ($search) {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('service_category_id')) {
            $query->where(
                'service_category_id',
                request('service_category_id')
            );
        }

        $allowedSorts = [
            'sort_order',
            'created_at',
            'title',
        ];

        $sort = request('sort');

        if ($sort) {
            $direction = str_starts_with($sort, '-')
                ? 'desc'
                : 'asc';

            $column = ltrim($sort, '-');

            if (in_array($column, $allowedSorts, true)) {
                $query->orderBy($column, $direction);
            }
        }

        $services = $query->paginate(10);

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