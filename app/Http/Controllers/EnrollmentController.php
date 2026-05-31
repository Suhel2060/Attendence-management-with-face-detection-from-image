<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class EnrollmentController extends Controller
{
    public function index()
    {
        $employees = User::all();
        $enrolled = $this->getEnrolledFromFastAPI();
        $enrolledImages = $this->getEnrolledImages($enrolled);
        return view('enrollment.index', compact('employees', 'enrolled', 'enrolledImages'));
    }

    public function enroll(Request $request)
    {
        $employeeId = $request->employee_id;
        $storageDir = "enrollment/{$employeeId}";
        $filenamesToKeep = [];

        if ($request->has('keep_images')) {
            $request->validate([
                'employee_id' => 'required|exists:users,employee_id',
                'keep_images' => 'array',
                'keep_images.*' => 'string',
                'new_images' => 'array',
                'new_images.*' => 'image',
            ]);

            $filenamesToKeep = $request->input('keep_images', []);

            foreach ($request->file('new_images') ?? [] as $i => $file) {
                $name = time() . "_{$i}." . $file->getClientOriginalExtension();
                $file->storeAs($storageDir, $name, 'public');
                $filenamesToKeep[] = $name;
            }

            if (count($filenamesToKeep) < 5) {
                return response()->json(['error' => "Total images must be at least 5 (you have " . count($filenamesToKeep) . ")."], 422);
            }
        } else {
            $request->validate([
                'employee_id' => 'required|exists:users,employee_id',
                'images' => 'required|array|min:5',
                'images.*' => 'required|image',
            ]);

            Storage::disk('public')->deleteDirectory($storageDir);

            foreach ($request->file('images') as $i => $file) {
                $name = time() . "_{$i}." . $file->getClientOriginalExtension();
                $file->storeAs($storageDir, $name, 'public');
                $filenamesToKeep[] = $name;
            }
        }

        $faceApiUrl = config('services.face_api.url');
        $httpRequest = Http::timeout(config('services.face_api.timeout', 10));

        foreach ($filenamesToKeep as $name) {
            $fullPath = Storage::disk('public')->path("{$storageDir}/{$name}");
            if (file_exists($fullPath)) {
                $httpRequest->attach('images', file_get_contents($fullPath), $name);
            }
        }

        $response = $httpRequest->post("$faceApiUrl/api/enroll?student_id=" . urlencode($employeeId));

        if (!$response->successful()) {
            return response()->json(['error' => 'Enrollment failed: ' . ($response->json()['detail'] ?? 'Unknown error')], 422);
        }

        if ($request->has('keep_images')) {
            $allFiles = Storage::disk('public')->files($storageDir);
            foreach ($allFiles as $file) {
                $basename = basename($file);
                if (!in_array($basename, $filenamesToKeep)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        return response()->json(['message' => 'Employee enrolled successfully.']);
    }

    public function destroy($employee_id)
    {
        $faceApiUrl = config('services.face_api.url');
        $response = Http::delete("$faceApiUrl/api/enroll/" . urlencode($employee_id));

        Storage::disk('public')->deleteDirectory("enrollment/{$employee_id}");

        if ($response->successful()) {
            return redirect()->route('enrollment.index')->with('success', "Employee $employee_id removed from face recognition.");
        }

        return redirect()->route('enrollment.index')->with('error', 'Failed to remove enrollment.');
    }

    private function getEnrolledFromFastAPI()
    {
        try {
            $faceApiUrl = config('services.face_api.url');
            $response = Http::timeout(3)->get("$faceApiUrl/api/enrolled");
            if ($response->successful()) {
                return $response->json()['students'] ?? [];
            }
        } catch (Exception $e) {

        }
        return [];
    }

    private function getEnrolledImages(array $enrolledIds)
    {
        $result = [];
        foreach ($enrolledIds as $id) {
            $dir = "enrollment/{$id}";
            if (Storage::disk('public')->exists($dir)) {
                $files = Storage::disk('public')->files($dir);
                $result[$id] = array_map(function ($f) {
                    return [
                        'url' => asset('storage/' . $f),
                        'name' => basename($f),
                    ];
                }, $files);
            } else {
                $result[$id] = [];
            }
        }
        return $result;
    }
}
