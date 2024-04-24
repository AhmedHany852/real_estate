<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $user;
    function __construct(User $user)
    {

        $this->user = $user;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with(['permissions'])->paginate($request->get('per_page', 50));

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date_format:Y/m/d',
            'national_id' => 'required|string|max:255',
            'photo' => 'nullable',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'email' => 'required|string|email|max:255' . $request->id,
            'password' => 'required|string|min:8',
            'roles_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if ($request->file('photo')) {
            $avatar = $request->file('photo');
            $avatar->store('uploads/personal_photo/', 'public');
            $photo = $avatar->hashName();
        } else {
            $photo = null;
        }

        $user = User::create([
            'name' => $request->name,
            'national_id' => $request->national_id,
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'photo' => $photo,
            'number' => $request->number,
            'email' => $request->email,
            'roles_name' => $request->roles_name,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole([$request->input('roles_name')]);

        return (new UserResource($user))
        ->response()
        ->setStatusCode(200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    
    public function update(Request $request, User $user )
    {
        $validator = Validator::make($request->all(), [

            'name' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'photo' => 'nullable',
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'email' => 'required|unique:users,email,' . $request->id,
            'password' => 'required|string|min:8',
            'roles_name' => 'required',
        ]);

        if ($validator->fails()) {
            // Debug statements
           // dd($request->all(), $validator->errors()->all());

            return response()->json(['errors' => $validator->errors()]);
        }

        if ($request->file('photo')) {
            $avatar = $request->file('photo');
            $avatar->store('uploads/personal_photo/', 'public');
            $photo = $avatar->hashName();
        } else {
            $photo = null;
        }

        // Update user details
        $user->update([
            'name' => $request->name,
            'national_id' => $request->national_id,
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'photo' => $photo,
            'number' => $request->number,
            'email' =>$request->email,
            'roles_name' => $request->roles_name,
            'password' => Hash::make($request->password),
        ]);

        // Delete existing roles
        DB::table('model_has_roles')->where('model_id', $user->id)->delete();

        // Assign new roles
        $user->assignRole([$request->input('roles_name')]);

        return (new UserResource($user))
        ->response()
        ->setStatusCode(200);
    }
    public function destroy(User $user)
    {

        // Delete personal photo if it exists
        if ($user->personal_photo) {
            // Assuming 'personal_photo' is the attribute storing the file name
            $photoPath = 'uploads/personal_photo/' . $user->personal_photo;

            // Delete photo from storage
            Storage::delete($photoPath);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
    public function getUserCount()
    {
        $count = User::count();

        return response()->json([
            "successful" => true,
            "message" => "عملية العرض تمت بنجاح",
            'data' => $count
        ]);
    }
}
