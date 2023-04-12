<?php

namespace App\Services;

use App\Exceptions\AuthException;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Repositories\RecoverPasswordTokenRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService implements UserInterface
{
    public function __construct(
        private readonly UserRepositoryInterface                 $userRepository,
        private readonly RecoverPasswordTokenRepositoryInterface $recoverPasswordTokenRepository
    ) {
    }

    /**
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        return $this->userRepository->create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => app('hash')->make($data['password'])
        ]);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->getUserById($id);
    }

    /**
     * @param string $email
     * @param string $password
     * @return array
     * @throws Exception
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->getUserByEmail($email);
        if (empty($user)) {
            throw new ModelNotFoundException('Email does not exist.');
        }

        if (!app('hash')->check($password, $user->password)) {
            throw new AuthException('Password is wrong.');
        }

        return $this->getJwt($user);
    }

    /**
     * @param User $user
     * @return array
     */
    protected function getJwt(User $user): array
    {
        $payload = [
            'iss' => config('app.url'),
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + config('security.token.expiration')
        ];

        return [
            'token' => JWT::encode(
                $payload,
                config('security.token.secret'),
                config('security.token.algorithm')
            ),
            'expiresAt' => $payload['exp']
        ];
    }

    /**
     * @param User $user
     * @return SentMessage|null
     */
    public function sendPasswordResetToken(User $user): ?SentMessage
    {
        $token = Str::uuid();
        $this->recoverPasswordTokenRepository->store($user->id, $token);
        return Mail::to($user->email)->send(new ResetPassword($token));
    }

    /**
     * @param string $token
     * @param string $password
     * @return void
     * @throws Exception
     */
    public function resetPassword(string $token, string $password): void
    {
        $tokenModel = $this->recoverPasswordTokenRepository->get($token);
        if (empty($tokenModel)) {
            throw new AuthException('No token is associated with this user.');
        }
        if ($tokenModel->is_used) {
            throw new AuthException('This token has already been used.');
        }
        $this->userRepository->update(
            $tokenModel->user_id,
            ['password' => app('hash')->make($password)]
        );
        $this->recoverPasswordTokenRepository->setIsUsed($tokenModel);
    }
}
