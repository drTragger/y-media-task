<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\MockInterface;
use Tests\TestCase;

class CompanyServiceTest extends TestCase
{
    private MockInterface $companyRepository;

    public function configure()
    {
        $this->companyRepository = \Mockery::mock('App\Repositories\CompanyRepository');
    }

    public function testCreate()
    {
        $this->configure();
        $company = Company::factory()->create();
        $this->companyRepository->expects('create')
            ->times(1)
            ->andReturn($company);

        $companyService = new CompanyService($this->companyRepository);

        $result = $companyService->create($company->user_id, $company->toArray());

        $this->assertInstanceOf(Company::class, $result);
        $this->assertEquals($company, $result);
    }

    public function testGetCompaniesForUser()
    {
        $this->configure();
        $collection = \Mockery::mock(Collection::class);
        $collection->expects('isEmpty')
            ->times(1)
            ->andReturn(false);
        $this->companyRepository->expects('getCompaniesForUser')
            ->times(1)
            ->andReturn($collection);
        $user = User::factory()->create();

        $companyService = new CompanyService($this->companyRepository);

        $result = $companyService->getCompaniesForUser($user->id);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testGetCompaniesForUserEmptySet()
    {
        $this->configure();
        $collection = \Mockery::mock(Collection::class);
        $collection->expects('isEmpty')
            ->times(1)
            ->andReturn(true);
        $this->companyRepository->expects('getCompaniesForUser')
            ->times(1)
            ->andReturn($collection);
        $user = User::factory()->create();

        $companyService = new CompanyService($this->companyRepository);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No companies are associated with this user.');

        $companyService->getCompaniesForUser($user->id);
    }
}
