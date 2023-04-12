<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

interface CompanyInterface
{
    public function create(int $userId, array $data): Company;
    public function getCompaniesForUser(int $userId): Collection;
}
