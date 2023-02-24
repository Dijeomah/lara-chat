<?php

    namespace App\Http\Controllers\AdminAuth;

    use App\Http\Controllers\Controller;
    use App\Models\Admin;
    use Illuminate\Contracts\Auth\Authenticatable;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use App\Models\User;

    class AdminAuthController extends Controller
    {

//        public function __construct()
//        {
//            $this->middleware('admin.verify', ['except' => ['login', 'register']]);
//        }

        public function me(): Authenticatable
        {
            return auth('admin')->user();
        }

        public function login(Request $request)
        {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('email', 'password');

            $token = Auth::attempt($credentials);
            if (!$token = auth('admin')->setTTL(60 * 24)->attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid email/password combination',
                ],  Response::HTTP_UNAUTHORIZED);
            }
            $user =  array_merge(['user' => $this->me()]);
            return response()->json([
                'status' => 'success',
                'admin' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        }

//        public function login(Request $request)
//        {
//
//            $credentials = $request->only(['email', 'password']);
//
//            if (!$token = auth('admin')->setTTL(60 * 24)->attempt($credentials)) {
//                throw new ExceptionWithStatus("Invalid email/password combination", Response::HTTP_UNAUTHORIZED);
//            }
//            return array_merge(['user' => $this->me()], $this->formatToken($token));
//        }


        public function register(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admins',
                'password' => 'required|string|min:6',
            ]);

            $user = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = Auth::login($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'admin' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        }

        public function logout()
        {
            Auth::logout();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        }

        public function refresh()
        {
            return response()->json([
                'status' => 'success',
                'admin' => Auth::user(),
                'authorisation' => [
                    'token' => Auth::refresh(),
                    'type' => 'bearer',
                ]
            ]);
        }

    }
