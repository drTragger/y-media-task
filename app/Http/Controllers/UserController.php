<?php

namespace App\Http\Controllers;

use App\Services\UserInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly UserInterface $userService
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $this->validate($request, [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
            'password' => ['required', 'max:50'],
        ]);

        $this->userService->register($request->all());
        return response()->json(
            ['status' => true, 'message' => 'Success'],
            Response::HTTP_CREATED
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        $token = $this->userService->login($request->post('email'), $request->post('password'));

        return response()->json(['status' => true, 'message' => $token]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendResetPasswordToken(Request $request): JsonResponse
    {
        $this->userService->sendPasswordResetToken($request->user);

        return response()->json(['status' => true, 'message' => 'Success']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        $this->userService->resetPassword(
            $request->input('token'),
            $request->input('password')
        );

        return response()->json(['status' => true, 'message' => 'Success']);
    }
}
