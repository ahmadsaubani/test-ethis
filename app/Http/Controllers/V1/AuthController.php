<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\CustomMessagesException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\UserTransformer;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    protected $client;
    
    public function __construct(Client $client, Request $request)
    {
        $this->client = $client;
    }

    public function register(Request $request) 
    {    
        DB::beginTransaction();
        try {
            $input = $request->only("name", "email", "password", "confirmation_password");
            $validator = validator($request->all(), [ 
                'name'                  => 'required',
                'email'                 => 'required|email',
                'password'              => 'required|min:5',  
                'confirmation_password' => 'required|same:password', 
            ]); 
    
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors());
            }    

            $check = User::whereEmail($input['email'])->exists();

            if ($check) {
                throw new CustomMessagesException("Duplicate Entry Exception", 403);
            }

            $input['password'] = Hash::make($input['password']);
            $input['email']     = trim(strtolower($input['email']));
            $user = User::create($input);

            DB::commit();
            return $this->showResult('data created', $user);

        } catch(Exception $e) {
            DB::rollBack();
            return $this->realErrorResponse($e);
        }
        
    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');

        $user = User::whereEmail(strtolower($input['email']))->first();

        if (!empty($user)) {
            if ($user->validateForPassportPasswordGrant($input['password'])) {
                if (! empty($user->tokens)) {
                    foreach ($user->tokens as $token) {
                        $token->revoked = true;
                        $token->save();
                    }
                }

                $success['token']       = $user->createToken('ethis')->accessToken;
                return $this->showResult('Data Found', [ 'data' => $success ]);
            } else {
                return $this->errorResponse('Wrong password', 401);
            }
        } else {
            return $this->errorResponse('User not found', 401);
        }
    }


    public function logout()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->token()->revoke();
            return $this->showResult('Logout success', [ 'data' => [] ]);
        }

        return $this->errorResponse('Unauthorised', 401);
    }
        
    public function getUserProfile()
    {
        $user = user();

        if (empty($user)) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->showResultV2('Data Found', $this->item($user, new UserTransformer()));
    }

    public function showAll()
    {
        $users = User::get();
        
        if (empty($users)) {
            return $this->errorResponse('Users is empty', 403);
        }

        return $this->showResultV2('Data Found', $this->collection($users, new UserTransformer()));
    }

} 

