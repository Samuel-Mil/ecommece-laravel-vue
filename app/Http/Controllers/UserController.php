<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string',
            'email'         => 'required|string|unique:users,email',
            'password'      => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->symbols()->numbers(),
            ],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::find($id);
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
        if (isset($request->email)) {
            return response(["message" => "You can not alter the email!"], 401);
        }

        $user = User::where('id', $id)->first();

        $request->validate([
            'name'          => 'string',
        ]);

        if($request->password){
            $request->password = bcrypt($request->password);
        }

        $user->update($request->all());

        return $user;
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        $data = $request->validate([
            'password' => 'required|string|confirmed',
        ]);

        if(Hash::check($data['password'], $user->password)){
            return response([
                "message" => "Your password cannot be the same as the previous one!"
            ], 401);
        }

        return $user->update([
            'password' => bcrypt($data['password'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->delete();
        return $user;
    }
}
