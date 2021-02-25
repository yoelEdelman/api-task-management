<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function task_index()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('/api/tasks', []);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function get_index_without_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/tasks', []);
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function task_index_completed()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $task = Task::create(['body'=>"some text", "completed" => 1, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('/api/tasks?completed=1', []);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function task_create()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post('/api/tasks', ['body' => 'some text',]);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function task_create_with_with_missing_required_fields()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post('/api/tasks', []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function task_show()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $task = Task::create(['body'=>"some text", "completed" => 0, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('/api/tasks/'.$task->id, []);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function task_show_unauthorised()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $task = Task::create(['body'=>"some text", "completed" => 0, 'user_id' => 999]);


        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('/api/tasks/'.$task->id, []);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function task_show_not_found()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token

        ])->get('/api/tasks/999', []);
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function task_destroy()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $task = Task::create(['body'=>"some text", "completed" => 0, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->delete('/api/tasks/'.$task->id, []);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function task_destroy_unauthorised()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $task = Task::create(['body'=>"some text", "completed" => 0, 'user_id' => 999]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->delete('/api/tasks/'.$task->id, []);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function task_update()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $task = Task::create(['body'=>"some text", "completed" => 0, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->put('/api/tasks/'.$task->id, ['body'=>"some text", "completed" => 0, 'user_id' => $user->id]);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function task_update_unauthorised()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $task = Task::create(['body'=>"some text", "completed" => 0, 'user_id' => 999]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->put('/api/tasks/'.$task->id, ['body'=>"some text", "completed" => 0, 'user_id' => $user->id]);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function task_update_not_found()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;
        $task = Task::create(['body'=>"some text", "completed" => 0, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->put('/api/tasks/'.$task->id, [ "completed" => 0, 'user_id' => $user->id]);
        $response->assertStatus(422);
    }
}
