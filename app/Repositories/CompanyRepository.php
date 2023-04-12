<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    /**
     * @param array $data
     * @return Company
     */
    public function create(array $data): Company
    {
        return Company::create($data);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getCompaniesForUser(int $userId): Collection
    {
        return Company::where('user_id', $userId)->get();
    }
}
