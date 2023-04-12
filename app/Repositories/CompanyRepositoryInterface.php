<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface
{
    public function create(array $data): Company;
    public function getCompaniesForUser(int $userId): Collection;
}
