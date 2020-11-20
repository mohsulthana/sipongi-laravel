<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\IssueTokenTrait;
use App\Http\Resources\Users\UserResource;
use App\Models\Passport\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use IssueTokenTrait;

    private $client;

    public function __construct()
    {
        $this->client = Client::query()->where('provider', 'users')->first();
    }

    public function login(Request $request)
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $messages = [
            'username.required' => __('validation.required', ['attribute' => 'Nama Pengguna']),
            'password.required' => __('validation.required', ['attribute' => 'Kata Sandi'])
        ];

        $this->validate($request, $rules, $messages);

        $user = User::query()
            ->where('username', $request->get('username'))
            ->first();

        if ($user) {
            if ($user->status) {
                $data = $this->issueToken($request, 'password');
                if ($data->getStatusCode() <= 200) {
                    return $data;
                }
            } else {
                return response()->json(['errors' => ['username' => [__('messages.account_blocked')]]], 422);
            }
        }

        return response()->json(['errors' => ['username' => [__('messages.username_pass_invalid')]]], 422);
    }

    public function me(Request $request)
    {
        $user = auth()->user();
        // if ($request->filled('tokenFirebase')) {
        //     $this->setFirebase($user, $request);
        // }
        UserResource::withoutWrapping();

        return new UserResource($user);
    }

    // protected function setFirebase($user, $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $query = UserTokenFirebase::query();
    //         $query->where('user_id', '=', $user->id);
    //         $query->where('token', '=', $request->get('tokenFirebase'));
    //         $query->where('access_token_id', '=', $user->token()->id);
    //         $count = $query->count();

    //         if ($count <= 0) {
    //             UserTokenFirebase::create([
    //                 'user_id' => $user->id,
    //                 'type' => $request->get('type') === 'web' ? 'web' : 'mobile',
    //                 'token' => $request->get('tokenFirebase'),
    //                 'access_token_id' => $user->token()->id,
    //                 'expires_at' => $user->token()->expires_at,
    //             ]);
    //         }
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return response()->json(array(
    //             'status' => false,
    //             'message' => __('exception.handler.500'),
    //             'errors' => $e
    //         ), 500);
    //     }

    //     DB::commit();
    // }

    public function refresh(Request $request)
    {
        $data = $this->issueToken($request, 'refresh_token');

        if ($data->getStatusCode() <= 200) {
            return $data;
        } else {
            return response()->json(['message' => 'invalid token.'], 400);
        }
    }

    public function updateAuthUserPassword(Request $request)
    {
        $rules = [
            'current' => 'required',
            'password' => 'required|strong_password|confirmed',
            'password_confirmation' => 'required'
        ];

        $messages = [
            'current.required' => __('validation.required', ['attribute' => __('fields.current_password')]),
            'password.required' => __('validation.required', ['attribute' => __('fields.new_password')]),
            'password.strong_password' => __('validation.strong_password', ['attribute' => __('fields.new_password')]),
            'password_confirmation.required' => __('validation.required', ['attribute' => __('fields.confirmation_new_password')]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('fields.new_password')]),
        ];

        $this->validate($request, $rules, $messages);

        try {
            $user = User::query()->find(auth()->user()->id);

            if (!Hash::check($request->get('current'), $user->password)) {
                return response()->json(['errors' => ['current' => [__('messages.wrong_password')]]], 422);
            }

            $user->default_pass = false;
            $user->password = Hash::make($request->get('password'));
            $user->save();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();

        return response()->json(array('status' => true), 200);
    }

    public function changeAvatar(Request $request)
    {
        $rules = [
            'file' => 'required|max:10240|mimes:jpeg,jpg,png',
        ];

        $messages = [
            'file.required' => __('validation.required', ['attribute' => __('fields.avatar_file')]),
            'file.max' => __('validation.max.file', ['attribute' => __('fields.avatar_file'), 'max' => '2', 'type' => 'MB']),
            'file.mimes' => __('validation.mimes', ['attribute' => __('fields.avatar_file'), 'values' => 'JPG, JPEG, PNG']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try {
            $user = auth()->user();

            if ($request->hasFile('file') && $user) {
                $oldfileexists = Storage::disk('publicNas')->exists('avatars/' . $user->id . '/' . $user->avatar);
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete('avatars/' . $user->id . '/' . $user->avatar);
                }
                $file = $request->file('file');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('nas/public/avatars/' . $user->id, $filename);

                $user->avatar = $filename;
                $user->save();
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();

        return response()->json(array('status' => true), 200);
    }

    public function logout(Request $request)
    {
        $accessToken = auth()->user()->token();

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update(['revoked' => true]);

        // DB::table('users_token_firebase')
        //     ->where('access_token_id', $accessToken->id)
        //     ->update(['revoked' => true]);

        $accessToken->revoke();

        return response()->json([], 204);
    }
}
