<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function register()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', [
            'name' => 'yoel',
            'email' => 'yoel@gmail.com',
            'password' => 'secret'
        ]);
        $response->assertStatus(201);
        $response->assertExactJson([
            'message' => 'You successfully registered',
        ]);
    }

    /**
     * @test
     */
    public function register_when_email_already_exist()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', [
            'email' => 'yoel@gmail.com',
            'password' => 'secret'
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function register_with_invalid_email()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', [
            'email' => 'yoel@gmail',
            'password' => 'secret'
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function login()
    {
        $user = User::create([
            'email' => 'yoel2@gmail.com',
            "name" => 'yoel',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', ['email' => 'yoel2@gmail.com', 'password' => 'secret']);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function login_with_invalid_info()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', [
            'email' => 'yoel@gmail.com',
            'password' => 'sec'
        ]);

        $response->assertStatus(404);
    }
}
