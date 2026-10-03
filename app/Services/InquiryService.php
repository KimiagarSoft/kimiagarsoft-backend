<?php

namespace App\Services;

use App\Models\Inquiry;

class InquiryService
{
    public function create(array $data): Inquiry
    {
        $data['status'] = 'new';

        return Inquiry::create($data);
    }

    public function update(Inquiry $inquiry, array $data): Inquiry
    {
        $inquiry->update($data);

        return $inquiry->refresh();
    }

    public function delete(Inquiry $inquiry): void
    {
        $inquiry->delete();
    }
}