<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;


class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
//        $this->authorize('isAdmin'); //it will affect all the functions in this controller

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {

            return User::latest()->paginate(5);

        }
//        $this->authorize('isAdmin');

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        if ($search = \Request::get('q')) {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            })->paginate(20);
        } else {
            $users = User::latest()->paginate(5);
        }
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $requested = $request->all();
        $requested['photo'] = 'profile.png';
        return User::create($requested);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function profile()
    {
//        authunticated user
        return auth('api')->user();
    }

    public function updateProfile(Request $request)
    {


        $user = auth('api')->user();

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|min:6'
        ]);

//        $currentPhoto = $user->photo;
//
//        if ($request->photo != $currentPhoto) {
//
//            $img = auth()->user()->id . '_' . $request->file('photo');
//            Image::make($request->image)->resize(250, 200)->save(public_path('images' . auth()->user()->id . '_' . $request->file('photo')->getClientOriginalName()));
//
//
//            $request['photo'] = $img;

//        $userPhoto = public_path('img/profile/').$currentPhoto;
//        if(file_exists($userPhoto)){
//            @unlink($userPhoto); //delete old photo
//        }

//
//        }
//
        if (!empty($request->password)) { //we check if he edit his password we hash it
            $request->merge(['password' => Hash::make($request['password'])]);
        }


        $user->update($request->except('photo'));
        return response('ok', 200);
    }

//    public function updateProfile(Request $request)
//    {
//
//
//        $user = auth('api')->user();
//        $currentPhoto = $user->photo;
//        if ($request->photo != $currentPhoto) {
//            $name = time() . '.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
//            Image::make($request->photo)->save(public_path('img/profile/') . $name);
//            $request->merge(['photo' => $name]);
//            $userPhoto = public_path('img/profile/') . $currentPhoto;
//            if (file_exists($userPhoto)) {
//                @unlink($userPhoto);
//            }
//        }
//
//        $user->update($request->all());
//        return response($name, 200);
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6'
        ]);

        $user->update($request->all());
//        return $id;

        return response('ok', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $this->authorize('isAdmin'); //it send an error and stop the function from continuoung
        $user = User::findOrFail($id);

        //delete user
        $user->delete();
        return response('ok', '200');
    }
}
