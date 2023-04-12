<?php

namespace App\Http\Controllers;

use App\Services\CompanyInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyInterface $companyService
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:companies,phone'],
            'description' => ['required', 'string'],
        ]);

        $this->companyService->create($request->user->id, $request->all());

        return response()->json(
            ['status' => true, 'message' => 'Success'],
            Response::HTTP_CREATED
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllForUser(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => [
                'companies' => $this->companyService->getCompaniesForUser($request->user->id)
            ]
        ]);
    }
}
