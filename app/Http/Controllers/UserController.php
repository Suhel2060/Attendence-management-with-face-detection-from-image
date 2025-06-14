<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Attendence;
use App\Models\Leaves;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    public function index(){
        $users = User::all();
        return view('pages.adduser', compact('users'));
    }
    public function AddUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'image' => 'required|file',
                'department' => 'required|string',
                'gender' => 'required|string',
                'date_of_birth_ad' => 'required|date',
                'phone_number' => 'required|regex:/^[0-9]{10}$/',
                'date_of_joining' => 'required|date',
                'address' => 'required|string'
            ]);

            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->gender = $data['gender'];
            $user->address = $data['address'];
            $user->department = $data['department'];
            $user->phone_number = $data['phone_number'];
            $user->date_of_joining = $data['date_of_joining'];
            $user->date_of_birth = $data['date_of_birth_ad'];
            $user->password = Hash::make($data['password']);
            $user->save();

            $role = $data['department'] === 'HR' ? 'Admin' : 'Employee';
            $user->assignRole($role);

            // Handle image upload
            $image = $request->file('image');
            $newName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $newName, 'public');
            $user->image = $imagePath;
            $user->save();


            $newKnownFaces=$user->employee_id.'.'.$image->getClientOriginalExtension();
            $image->storeAs('known_faces', $newKnownFaces, 'public');

            DB::commit();
            return response()->json([
                'message' => 'User added successfully',
                'user' => $user,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to store the user'], 500);
        }
    }

    public function UpdateUser(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => "required|email|unique:users,email,{$user->id}",
                'password' => 'nullable|string|min:8',
                'image' => 'nullable|file',
                'department' => 'required|string',
                'gender' => 'required|string',
                'date_of_birth_ad' => 'required|date',
                'phone_number' => 'required|regex:/^[0-9]{10}$/',
                'date_of_joining' => 'required|date',
                'address' => 'required|string'
            ]);

            // Update basic fields
            $user->name = $data['name'];
            $user->email = $data['email'];
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->gender = $data['gender'];
            $user->address = $data['address'];
            $user->department = $data['department'];
            $user->phone_number = $data['phone_number'];
            $user->date_of_joining = $data['date_of_joining'];
            $user->date_of_birth = $data['date_of_birth_ad'];
            $user->save();

            // Update role if department changed
            $role = $data['department'] === 'HR' ? 'Admin' : 'Employee';
            if (!$user->hasRole($role)) {
                $user->syncRoles([$role]);
            }

            // Handle image replacement
            if ($request->hasFile('image')) {
                // Delete previous image
                if ($user->image && Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                    $knownfaces=explode('.', $user->image);
                    $knownFaceimage=$user->employee_id.'.'.$knownfaces[1];
                    if (Storage::disk('public')->exists('known_faces/'.$knownFaceimage)) {
                        Storage::disk('public')->delete('known_faces/'.$knownFaceimage);
                    }
                }
                // Store new image
                $image = $request->file('image');
                $newName = Str::random(20) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $newName, 'public');
                $user->image = $imagePath;
                $user->save();

                $newKnownFaces=$user->employee_id.'.'.$image->getClientOriginalExtension();
                $image->storeAs('known_faces', $newKnownFaces, 'public');
            }

            DB::commit();
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json(['error' => 'Failed to update the user'], 500);
        }
    }

    public function DeleteUser($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);

            // Delete image file if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $knownfaces=explode('.', $user->image);
            $knownFaceimage=$user->employee_id.'.'.$knownfaces[1];
            if (Storage::disk('public')->exists('known_faces/'.$knownFaceimage)) {
                Storage::disk('public')->delete('known_faces/'.$knownFaceimage);
            }

            Attendence::where('employee_id', $user->employee_id)->delete();
            Leaves::where('employee_id', $user->employee_id)->delete();
            

            $user->delete();
            DB::commit();

            return response()->json(['message' => 'User deleted successfully']);
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete the user'], 500);
        }
    }
}
