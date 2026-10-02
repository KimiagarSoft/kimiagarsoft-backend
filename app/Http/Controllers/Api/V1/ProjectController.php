<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            $this->projectService->list()
        );
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project);
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        $project = $this->projectService->create(
            $request->validated()
        );

        return new ProjectResource($project);
    }

    public function update(
        UpdateProjectRequest $request,
        Project $project
    ): ProjectResource {
        $project = $this->projectService->update(
            $project,
            $request->validated()
        );

        return new ProjectResource($project);
    }

    public function destroy(Project $project): Response
    {
        $this->projectService->delete($project);

        return response()->noContent();
    }
}