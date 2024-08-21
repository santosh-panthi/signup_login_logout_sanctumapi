<?php

    namespace App\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Validator;

    class AuthController extends Controller
    {
        public function singup(Request $request)
        {
            $validateData = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required'
                ]

            );
            if ($validateData->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateData->error()->all()
                ], 401);
            }
            $user = User::create([

                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,

            ]);
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => $user,
            ], 200);
        }
        public function login(Request $request)
        {
            $validateData = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );
            if ($validateData->fails()) {
                return response()->json([
                    'status' => false,
                    "message" => 'Validation Error',
                    'error' => $validateData->errors()->all(),
                ], 404);
            }
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password,])) {
                $authUser = Auth::user();
                return response()->json([
                    'status' => true,
                    'message' => 'User logged in successfully',
                    'token'=> $authUser->createToken('API Token')->plainTextToken,
                    'token_type'=>'bearer',
                ], 200);
            }
            else {
                return response()->json([
                    'status' => false,
                    'message'=>'Invalid Email or Password',
                ],401);
            }
        }
        public function logout(Request $request){
            // $user = $request->user()->token()->delete();
              $result = $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => true,
                'user'=> $result,
                'message' => 'User logged out successfully',
                
            ], 200);

        }
    }
