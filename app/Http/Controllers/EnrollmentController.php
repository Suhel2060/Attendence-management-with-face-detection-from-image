<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Services\FaceRecognitionService;
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

    public function enroll(Request $request, FaceRecognitionService $faceService)
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

        $imagePaths = [];
        foreach ($filenamesToKeep as $name) {
            $fullPath = Storage::disk('public')->path("{$storageDir}/{$name}");
            if (file_exists($fullPath)) {
                $imagePaths[] = $fullPath;
            }
        }

        $result = $faceService->enroll($employeeId, $imagePaths);

        if (($result['status'] ?? '') === 'error') {
            $errorMsg = 'Enrollment failed on one or both systems.';
            if (isset($result['results'])) {
                foreach ($result['results'] as $key => $res) {
                    if (isset($res['detail'])) {
                        $errorMsg .= " {$key}: {$res['detail']}";
                    }
                }
            }
            return response()->json(['error' => $errorMsg], 422);
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

        return response()->json(['message' => 'Employee enrolled successfully on both systems.']);
    }

    public function destroy($employee_id, FaceRecognitionService $faceService)
    {
        $results = $faceService->removeEnrollment($employee_id);

        Storage::disk('public')->deleteDirectory("enrollment/{$employee_id}");

        $hasSuccess = false;
        foreach ($results as $name => $result) {
            if (($result['status'] ?? '') !== 'error') {
                $hasSuccess = true;
            }
        }

        if ($hasSuccess) {
            return redirect()->route('enrollment.index')->with('success', "Employee $employee_id removed from face recognition (both systems).");
        }

        return redirect()->route('enrollment.index')->with('error', 'Failed to remove enrollment from either system.');
    }

    private function getEnrolledFromFastAPI()
    {
        try {
            $faceService = app(FaceRecognitionService::class);
            return $faceService->getEnrolled();
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
