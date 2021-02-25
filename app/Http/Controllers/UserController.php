<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class UserController extends Controller
{

    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"users"},
     *     @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=false,
     *      ),
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true,
     *      ),
     *     @OA\Parameter(
     *          name="password",
     *          in="query",
     *          required=true,
     *      ),
     *     @OA\Response(
     *          response="201",
     *          description="User registration",
     *          @OA\JsonContent(type="string")
     *      ),
     * )
     *
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'name' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response(['message' => 'You successfully registered'], 201);
    }

    /**
     *     @OA\Post(
     *     path="/login",
     *     tags={"users"},
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true,
     *      ),
     *     @OA\Parameter(
     *          name="password",
     *          in="query",
     *          required=true,
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="User login",
     *          @OA\JsonContent(type="integer", ref="#/components/schemas/User"),
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Incorrect login or password.",
     *          @OA\JsonContent(type="string", default="incorrect login or password.")
     *      ),
     * )
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['incorrect login or password.']
            ], 404);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response(['user' => $user, 'token' => $token], 200);
    }
}
