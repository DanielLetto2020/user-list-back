<?php

namespace App\Http\Controllers;

use App\Enums\EnumAppType;
use App\Enums\EnumUserAccessLevel;
use App\Http\Resources\User\RUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    final public function register(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        DB::beginTransaction();

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->abilities = [EnumUserAccessLevel::USER];
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $token = $user->createToken(EnumAppType::WEB_USER, [EnumUserAccessLevel::USER])->plainTextToken;

        DB::commit();

        return response()->json(['token' => $token]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    final public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::query()->where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw new \RuntimeException('нет юзера', 222);
        }

        $token = $user->createToken(EnumAppType::WEB_USER, $user->abilities ?? [])->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    final public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => true]);
    }

    /**
     * @param Request $request
     * @return RUser
     */
    final public function getUser(Request $request): RUser
    {
        return RUser::make($request->user());
    }
}
