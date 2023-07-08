<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUserFormRequest;
use App\Http\Requests\UpdateUserFormRequest;
use Illuminate\Auth\Events\Registered;

 /**
 * @group  User
 *
 * APIs for managing users
 */

class UserController extends Controller
{

    private $user;
    private $totalPage = 20;

    public function __construct(User $user )
    {
        $this->user = $user;
    }

     /**
     * List with paginate and search
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = $this->user->getResults($request->all(), $this->totalPage);

        return response()->json($users, 200);
    }


     /**
     * List all records without paginate
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return response()->json($this->user->get(), 200);
    }



    /**
     * Create User
     *
     * @bodyParam name required string  Name
     * @bodyParam username required string  Name
     * @bodyParam password string
     * @bodyParam active char(1)    1: Active | 0: disabled
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(StoreUserFormRequest $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $data['active']   = 1;
        $user = $this->user->create($data);

        event(new Registered($user));

        return response()->json(compact('user'), 201);
    }

    /**
     * Get User By ID
     *
     * @urlParam  id required The ID of the User
     *
     * @param  \App\Models\User
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->user->find($id);

        if (!$user)
            return response()->json(['error' => 'User not Found'], 404) ;

        return response()->json($user, 200);

    }


    /**
     * Update User
     *
     * @urlParam  id required The ID of the User
     * @bodyParam name string  Name
     * @bodyParam username string  Name
     * @bodyParam password string
     * @bodyParam active char(1)    1: Active | 0: disabled
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserFormRequest $request, User $user)
    {
        $data = $request->all();
        $user = $this->user->find($user->id);

        if (!$user)
            return response()->json(['error' => 'User not Found'], 404);

        if (isset($request->password))
            $data['password'] = bcrypt($data['password']);

        $user->update($data);

        return response()->json($user, 200);
    }


    /**
     * Delete User
     *
     * @urlParam  id required The ID of the User
     *
     * @param  \App\Models\User
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->user->find($id);

        if (!$user)
            return response()->json(['error' => 'User not Found'], 404);

        $user->delete();

        return response()->json(['message' => 'User deleted', 'success' => true], 204);
    }





}
