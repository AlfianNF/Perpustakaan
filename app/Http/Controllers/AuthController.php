<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/register",
     * summary="Register new user",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="no_induk", type="string", example="12345"),
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", example="john.doe@example.com"),
     * @OA\Property(property="password", type="string", example="password123"),
     * @OA\Property(property="is_admin", type="boolean", example=false),
     * )
     * ),
     * @OA\Response(response=201, description="User created successfully."),
     * @OA\Response(response=422, description="Validation failed."),
     * @OA\Response(response=500, description="Server error."),
     * )
     */
    public function register(Request $request)
    {
        try {
            $rules = User::getValidationRules('add');
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $request->all();
            $data['password'] = Hash::make($data['password']); // Hash password

            $user = User::create($data);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat.',
                'data' => $user,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada database.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Login user",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="no_induk", type="string", example="12345"),
     * @OA\Property(property="password", type="string", example="password123"),
     * )
     * ),
     * @OA\Response(response=200, description="Login successful."),
     * @OA\Response(response=401, description="Invalid login credentials."),
     * @OA\Response(response=422, description="Validation failed."),
     * @OA\Response(response=500, description="Server error."),
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'no_induk' => 'required',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid login credentials.',
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User berhasil login',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil logout',
        ]);
    }
}