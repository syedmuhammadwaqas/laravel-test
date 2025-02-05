<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:10',
            'email' => 'required|email|regex:/^[^@]+@[^@]+\.[^@]+$/|not_regex:/gmail\.com$/i',
            'phone' => 'required|regex:/^\+\d+$/',
            'message' => 'required|min:10',
            'street' => 'nullable',
            'state' => 'nullable',
            'zip' => 'nullable',
            'country' => 'nullable',
            'images.*' => 'file|mimes:jpg,jpeg',
            'files.*' => 'file|mimes:pdf'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['images', 'files']);
        
        // Process file uploads
        $data['images'] = $this->storeFiles($request->file('images'));
        $data['files'] = $this->storeFiles($request->file('files'));

        $submission = Submission::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Submission saved successfully'
        ]);
    }

    private function storeFiles($files)
    {
        if (!$files) return null;
        
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store('submissions', 'files');
        }
        return $paths;
    }
} 