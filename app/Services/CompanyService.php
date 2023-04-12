<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\CompanyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyService implements CompanyInterface
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository
    ) {
    }

    /**
     * @param int $userId
     * @param array $data
     * @return Company
     */
    public function create(int $userId, array $data): Company
    {
        return $this->companyRepository->create([
            'user_id' => $userId,
            'title' => $data['title'],
            'phone' => $data['phone'],
            'description' => $data['description']
        ]);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getCompaniesForUser(int $userId): Collection
    {
        $companies = $this->companyRepository->getCompaniesForUser($userId);
        if ($companies->isEmpty()) {
            throw new ModelNotFoundException('No companies are associated with this user.');
        }
        return $companies;
    }
}
