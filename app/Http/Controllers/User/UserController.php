<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Hash;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
            ]
        );

        $data['password'] = Hash::make($data['password']);
        $data['verified'] = User::USER_NOT_VERIFIED;
        $data['verification_token'] = User::generateVerificationToken();
        $data['admin'] = User::USER_NOT_VERIFIED;

        $user = User::create($data);

        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$user->id,
                'password' => 'required|min:6|confirmed',
                'admin' => 'in:' . User::USER_ADMIN . ',' . User::USER_NOT_ADMIN,
            ]
        );

        $user->name = $data['name'];

        if ($request->has('email') && $request->email != $user->email) {
            $user->verified = User::USER_NOT_VERIFIED;
            $user->verification_token = User::generateVerificationToken();
        }

        if ($request->has('password')) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse(
                    'Se requiere ser usuario veriificado para cambiar estado de administración',
                    409
                );
            }
        }

        if (!$user->isDirty()) {
            return $this->errorResponse(
                'Especifica almenos un campo para actualizar',
                422
            );
        }

        $user->update($data);

        return $this->showOne($user);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user);
    }
}
