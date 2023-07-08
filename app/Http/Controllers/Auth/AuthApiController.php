<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;

class AuthApiController extends Controller
{

    use AuthenticatesUsers;

    protected $maxAttempts = 5; // Default is 5
    protected $decayMinutes = 1; // Default is 1

    public function authenticate(Request $request, User $user)
    {

        $credentials = $this->getCredentials();

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
           return response()->json(['error' => 'Muitas tentativas de Login, usuário temporariamente bloqueado, tente novamente em 60 minutos'], 401);
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                $this->incrementLoginAttempts($request);
                return response()->json(['error' => 'Login Não Autorizado'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }


        // Get user authenticated
        $user = auth()->user();

        // invalidate users if already logged in other devices
        $this->invalidateUserPreviousTokens($user);

        // verify if user already actived by email
        if(!$this->userIsVerified($user, $token)){
            return response()->json(['error' => 'Email não verificado', 'success' => false], 403);
        }

        // save login logs
        $this->saveUserLoginLogs($user, $request, $token);

        // all good so return the token
        return $this->respondWithTokenAndUser($token, $user);
    }



    /**
     * Invalidate a token (add it to the blacklist).
     *
     * @param  $user
     * @param  bool  $forceForever
     * @return $this
     */
    protected function invalidateUserPreviousTokens($user, $forceForever = false)
    {
        if (isset($user->lastest_token) && (!empty($user->lastest_token))) {

            $ttl = auth()->factory()->getTTL();
            $diff_in_minutes = $this->getUserLastLoginTimeDiffTokenTtl($user);

            if ($diff_in_minutes < $ttl) {
                JWTAuth::setToken($user->lastest_token);
                JWTAuth::invalidate($forceForever);
            }
        }

    }


    protected function getUserLastLoginTimeDiffTokenTtl($user){

        $to     = Carbon::parse($user->last_login_at);
        $from   = Carbon::now();
        return $to->diffInMinutes($from);

    }

    protected function userIsVerified($user, $token)
    {
        if(!$user->email_verified_at)
           return false;

        return true;
    }

    protected function saveUserLoginLogs($user, $request, $token)
    {
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'last_login_at'     => Carbon::now()->toDateTimeString(),
                'last_login_ip'     => $request->getClientIp(),
                'last_login_host'   => $request->getHost(),
                'lastest_token'     => $token,
            ]);

    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithTokenAndUser($token, $user)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ]);
    }


    public function getCredentials()
    {

        $credentials = request()->only('email', 'password');
        $credentials['active'] = 1;

        return $credentials;
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiLogout(Request $request)
    {
       auth()->invalidate(true);
       return response()->json(['message' => 'Logout', 'success' => true]);
    }


    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (JWTException $e) {

            return response()->json(['token_absent'], 403);

        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

    public function refreshToken()
    {
        if (!$token = JWTAuth::getToken())
            return response()->json(['error' => 'token_not_send'], 401);

        try {
            $token = JWTAuth::refresh();
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }


        return response()->json(compact('token'));

    }



}
