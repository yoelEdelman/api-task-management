<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     *      @OA\Get(
     *     path="/tasks",
     *     tags={"tasks"},
     *     @OA\Parameter(
     *          name="completed",
     *          description="Les task completed",
     *          in="path",
     *          required=false,
     *          @OA\Schema(type="boolean")
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="Task index",
     *          @OA\JsonContent(ref="#/components/schemas/Task"),
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="unauthorised.",
     *          @OA\JsonContent(type="string", default="unauthorised")
     *      ),
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::user()->id);

        $tasks = $user->tasks()->orderBy('updated_at', 'desc')
            ->when(request('completed'), function ($query) {
                $query->where('completed', 1);
            })
            ->get();

        return response(['tasks' => $tasks]);
    }

    /**
     *     @OA\Post (
     *     path="/tasks",
     *     tags={"tasks"},
     *     @OA\Parameter(
     *          name="body",
     *          description="Body de la task",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="completed",
     *          description="Task fini ? oui/non",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="Show task",
     *          @OA\JsonContent(ref="#/components/schemas/Task"),
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="unauthorised.",
     *          @OA\JsonContent(type="string", default="unauthorised")
     *      ),
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), ['body' => ['required', 'string'],]);

        if ($validation->fails()) {
            $message = $validation->messages()->toArray();
            return response()->json(['error' => $message], 422);
        }

        $user = User::find(Auth::user()->id);
        $task = new Task(
            [
                'body' => $request['body'],
                'user_id' => Auth::user()->id,
                'completed' => 0
            ]
        );
        $task = $user->tasks()->save($task);

        return response()->json([$task], 200);
    }

    /**
     *     @OA\Get(
     *     path="/tasks/{id}",
     *     tags={"tasks"},
     *     @OA\Parameter(
     *          name="id",
     *          description="ID de la task",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="Show task",
     *          @OA\JsonContent(ref="#/components/schemas/Task"),
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="task not found.",
     *          @OA\JsonContent(type="string", default="task not found")
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="unauthorised.",
     *          @OA\JsonContent(type="string", default="unauthorised")
     *      ),
     * )
     *
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::find($id);

        if(!$task){
            return response()->json(["task not found"], 404);
        }

        if ($task->user_id != Auth::user()->id) {
            return response()->json(['unauthorised'], 403);
        }

        return response()->json([$task], 200);
    }

    /**
     *     @OA\Put  (
     *     path="/tasks/{id}",
     *     tags={"tasks"},
     *     @OA\Parameter(
     *          name="ID",
     *          description="ID de la task",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Parameter(
     *          name="body",
     *          description="Body de la task",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="completed",
     *          description="Task fini ? oui/non",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="Show task",
     *          @OA\JsonContent(ref="#/components/schemas/Task"),
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="task not found.",
     *          @OA\JsonContent(type="string", default="task not found")
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="unauthorised.",
     *          @OA\JsonContent(type="string", default="unauthorised")
     *      ),
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, Task $task)
    {
        $task = Task::findOrFail($request['id']);

        if ($task->user_id != Auth::user()->id) {
            return response()->json(['unauthorised'], 403);
        }

        $validation = Validator::make($request->all(), [
            'body' => ['required', 'string'],
            'completed' => ['required', 'integer']
        ]);

        if ($validation->fails()) {
            $message = $validation->messages()->toArray();
            return response()->json(['error' => $message], 422);
        }

        $task->update([
            'body' => $request->body,
            'completed' => $request->completed,
        ]);

        return response()->json(['error' => $task], 200);
    }

    /**
     *     @OA\Delete(
     *     path="/tasks/{id}",
     *     tags={"tasks"},
     *     @OA\Parameter(
     *          name="id",
     *          description="ID de la task",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="Delete task",
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="task not found.",
     *          @OA\JsonContent(type="string", default="task not found")
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="unauthorised.",
     *          @OA\JsonContent(type="string", default="unauthorised")
     *      ),
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Task $task)
    {
        $task = Task::find($id);

        if(!$task){
            return response()->json(["task not found"], 404);
        }

        if ($task->user_id != Auth::user()->id) {
            return response()->json(['unauthorised'], 403);
        }

        $task->delete();

        return response()->json([], 200);
    }
}
