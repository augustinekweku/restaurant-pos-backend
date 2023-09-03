<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Role;
use App\Models\User;

class AdminController extends BaseController
{
    public function createUser(Request $request)
    {
        $this->validate($request, [
            'firstName' => 'required',
            'lastName' => 'required',
            'phone' => 'required',
            'userType' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        try {
            //code...
            $password = bcrypt($request->password);
            if ($request->role_id) {
                $user = User::create([
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'email' => $request->email,
                    'password' => $password,
                    'phone' => $request->phone,
                    'userType' => $request->userType,
                    'role_id' => $request->role_id,
                ]);
            } else {
                $user = User::create([
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'email' => $request->email,
                    'password' => $password,
                    'phone' => $request->phone,
                    'userType' => $request->userType,
                ]);
            }
            return $this->sendResponse($user, 'User created successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
    public function editUser(Request $request)
    {
        $this->validate($request, [

            'id' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'phone' => 'required',
            'userType' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",
            'password' => 'min:6',
        ]);

        try {
            //code...
            $data = [
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'phone' => $request->phone,
                'userType' => $request->userType,
            ];
            if ($request->password) {
                $password = bcrypt($request->password);
                $data['password'] = $password;
            }
            if ($request->role_id) {
                $data['role_id'] = $password;
            }

            $user = User::where('id', $request->id)->update($data);
            return $this->sendResponse($user, 'User updated successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }

    public function getUsers(Request $request)
    {

        try {
            //code...
            $res = User::get();
            return $this->sendResponse($res, 'Users retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Users not retrieved', $th->getMessage(), 500);
        }
    }
    public function deleteUser(Request $request)
    {
        //validate request
        $this->validate($request, [
            'id' => 'required'
        ]);
        try {
            //code...
            $res =  User::where('id', $request->id)->delete();
            return $this->sendResponse($res, 'User deleted successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }


    public function getRoles()
    {
        try {
            //code...
            $res = Role::get();
            return $this->sendResponse($res, 'Roles retrieved successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Roles not retrieved', $th->getMessage(), 500);
        }
    }

    public function createRole(Request $request)
    {
        //validate request
        $this->validate($request, [
            'role_name' => 'required'
        ]);
        try {
            //code...
            $res =  Role::create([
                'role_name' => $request->role_name,
            ]);
            return $this->sendResponse($res, 'Role created successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
}
