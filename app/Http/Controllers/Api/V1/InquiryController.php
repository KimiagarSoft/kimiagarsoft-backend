<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Requests\UpdateInquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Models\Inquiry;
use App\Services\InquiryService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class InquiryController extends Controller
{
    public function __construct(
        private readonly InquiryService $inquiryService
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function index()
    {
        Gate::authorize('viewAny', Inquiry::class);

        return InquiryResource::collection(
            Inquiry::query()
                ->latest()
                ->get()
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Inquiry $inquiry): InquiryResource
    {
        Gate::authorize('view', $inquiry);

        return new InquiryResource($inquiry);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(
        UpdateInquiryRequest $request,
        Inquiry $inquiry
    ): InquiryResource {
        Gate::authorize('update', $inquiry);

        $inquiry = $this->inquiryService->update(
            $inquiry,
            $request->validated()
        );

        return new InquiryResource($inquiry);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Inquiry $inquiry): JsonResponse
    {
        Gate::authorize('delete', $inquiry);

        $this->inquiryService->delete($inquiry);

        return response()->json(null, 204);
    }

    public function store(StoreInquiryRequest $request): JsonResponse
    {
        $inquiry = $this->inquiryService->create(
            $request->validated()
        );

        return (new InquiryResource($inquiry))
            ->response()
            ->setStatusCode(201);
    }
}
