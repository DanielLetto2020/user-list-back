<?php

namespace App\Http\Controllers;

use App\Enums\EnumAppType;
use App\Enums\EnumUserAccessLevel;
use App\Http\Resources\User\RUser;
use App\Http\Resources\User\RUserOther;
use App\Http\Resources\UserPayment\RUserPayments;
use App\Models\User;
use App\Models\UserPayment;
use http\Exception\RuntimeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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

    /**
     * @return AnonymousResourceCollection
     */
    final public function list(): AnonymousResourceCollection
    {
        $users = User::query()->get();
        return RUserOther::collection($users);
    }

    /**
     * @throws \Exception
     */
    final public function createOrUpdate(Request $request)
    {
        $request->validate([
            'id' => 'nullable',
            'phone' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required_without:id',
        ]);

        DB::beginTransaction();

        $user = $request->has('id') ? User::query()->find($request->input('id')) : new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        if ($request->has('password')) {
            $user->abilities = [EnumUserAccessLevel::USER];
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        for ($i = 1; $i <= 10; $i++) {
            $paymentsTemp = new UserPayment();
            $paymentsTemp->user_id = $user->id;
            $paymentsTemp->amount = random_int(100, 20000);
            $paymentsTemp->status = 'в обработке';
            $paymentsTemp->save();
        }
        DB::commit();
    }

    final public function delete(Request $request)
    {
        $user = User::query()->find($request->id);

        DB::beginTransaction();
        UserPayment::query()->where('user_id', $user->id)->delete();
        $user ? $user->delete() : throw new \RuntimeException('нет юзера', 222);
        DB::commit();

        return response()->json(['message' => true]);
    }

    final public function show($id)
    {
        $user = User::query()->find($id);
        return RUserOther::make($user);
    }

    final public function payments($id)
    {
        $payments = UserPayment::query()->where('user_id', $id)->get();
        return $payments ? RUserPayments::collection($payments) : response()->json(['data' => []]);
    }
}
