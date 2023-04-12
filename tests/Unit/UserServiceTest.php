<?php

namespace Tests\Unit;

use App\Exceptions\AuthException;
use App\Models\RecoverPasswordToken;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\MockInterface;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private MockInterface $userRepository;
    private MockInterface $recoverPasswordTokenRepository;

    public function configure()
    {
        $this->userRepository = \Mockery::mock('App\Repositories\UserRepository');
        $this->recoverPasswordTokenRepository = \Mockery::mock('App\Repositories\RecoverPasswordTokenRepository');
    }

    public function testRegister()
    {
        $this->configure();
        $this->userRepository->shouldReceive('create')
            ->times(1)
            ->andReturn(User::factory()->create());

        $userService =  new UserService(
            $this->userRepository,
            $this->recoverPasswordTokenRepository
        );

        $result = $userService->register([
            'firstName' => 'Name',
            'lastName' => 'Lastname',
            'email' => 'test@mail.com',
            'phone' => '1234567',
            'password' => 'qwerty'
        ]);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testGetUserById()
    {
        $this->configure();
        $user = User::factory()->create();
        $this->userRepository->shouldReceive('getUserById')
            ->times(1)
            ->andReturn($user);

        $userService = new UserService(
            $this->userRepository,
            $this->recoverPasswordTokenRepository
        );

        $result = $userService->getUserById($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user, $result);
    }

    public function testLoginWrongPassword()
    {
        $this->configure();
        $user = User::factory()->create();
        $this->userRepository->shouldReceive('getUserByEmail')
            ->times(1)
            ->andReturn($user);

        $userService = new UserService(
            $this->userRepository,
            $this->recoverPasswordTokenRepository
        );

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Password is wrong.');

        $userService->login($user->email, 'wrong_password');
    }

    public function testLogin()
    {
        $this->configure();
        $user = User::factory()->create();
        $newPassword = 'new_password';
        $user->password = app('hash')->make($newPassword);
        $this->userRepository->shouldReceive('getUserByEmail')
            ->times(1)
            ->andReturn($user);

        $userService = new UserService(
            $this->userRepository,
            $this->recoverPasswordTokenRepository
        );

        $result = $userService->login($user->email, $newPassword);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('expiresAt', $result);
    }

    public function testLoginWrongEmail()
    {
        $this->configure();
        $this->userRepository->shouldReceive('getUserByEmail')
            ->times(1)
            ->andReturn(null);

        $userService = new UserService(
            $this->userRepository,
            $this->recoverPasswordTokenRepository
        );

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Email does not exist.');

        $userService->login('wrongEmail@mail.com', 'wrong_password');
    }

    public function testResetPasswordTokenIsUsed()
    {
        $this->configure();
        $token = RecoverPasswordToken::factory()->create();
        $newPassword = 'new_password';
        $token->is_used = true;
        $this->recoverPasswordTokenRepository->expects('get')
            ->times(1)
            ->andReturn($token);
        $this->recoverPasswordTokenRepository->expects('setIsUsed')
            ->times(0);
        $this->userRepository->expects('update')
            ->times(0);

        $userService = new UserService(
            $this->userRepository,
            $this->recoverPasswordTokenRepository
        );

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('This token has already been used.');

        $userService->resetPassword($token->token, $newPassword);
    }
}
