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
use Illuminate\Support\Facades\Http;
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
                if ($user->image && Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }
                $image = $request->file('image');
                $newName = Str::random(20) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $newName, 'public');
                $user->image = $imagePath;
                $user->save();
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
            $empId = $user->employee_id;

            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            Attendence::where('employee_id', $empId)->delete();
            Leaves::where('employee_id', $empId)->delete();

            Http::timeout(5)->delete(config('services.face_api.url') . '/api/enroll/' . urlencode($empId));
            Storage::disk('public')->deleteDirectory("enrollment/{$empId}");

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
