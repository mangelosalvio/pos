<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::with('roles')->get();
        //return User::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['name'] = $data['fullname'];
        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'password' => 'required|confirmed|min:6',
            'username' => 'required|unique:users|max:255',
            'role_id' => 'required',
        ]);
        if ( !$validator->fails() ) {
            $User = User::create([
                'name' => $data['name'],
                'password' => bcrypt($data['password']),
                'username' => $data['username']
            ]);
            $User->attachRole(Role::find($data['role_id']));
        }

        return $validator->errors()->all();;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $User = User::find($id);
        $data = $request->all();

        $data['name'] = $data['fullname'];

        if ( $request->has('password') ) {
            $validator = Validator::make($data, [
                'password' => 'required|confirmed|min:6',
            ]);

            if ( !$validator->fails() ) {
                $User->update([
                    'password' => bcrypt($data['password'])
                ]);
            }
        } else {
            $validator = Validator::make($data, [
                'name' => 'required|max:255',
                'username' => 'required|unique:users,username,'.$User->id.'|max:255',
                'role_id' => 'required',
            ]);
        }


        if ( !$validator->fails() ) {
            $User->update([
                'name' => $data['name'],
                'username' => $data['username']
            ]);

            if ( $User->roles()->find($data['role_id']) == null ) {
                $User->roles()->detach();
                $User->attachRole(Role::find($data['role_id']));
            }
        }

        return $validator->errors()->all();;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return User::with('roles')->get();
    }
}
