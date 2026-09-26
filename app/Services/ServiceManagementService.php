<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ServiceManagementService
{
    /**
     * Create a new service.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Service
    {
        return DB::transaction(function () use ($data): Service {
            return Service::create($data);
        });
    }

    /**
     * Update an existing service.
     *
     * @param array<string, mixed> $data
     */
    public function update(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data): Service {
            $service->update($data);

            return $service->refresh();
        });
    }

    /**
     * Delete an existing service.
     */
    public function delete(Service $service): void
    {
        DB::transaction(function () use ($service): void {
            $service->delete();
        });
    }
}

