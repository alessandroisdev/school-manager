<?php

namespace App\Interfaces\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Handle WYSIWYG image uploads
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // max 5MB
        ]);

        $unitId = session('active_unit_id', 'global');
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store the file in the public disk, under unit's folder
            $path = $file->storeAs("uploads/units/{$unitId}/images", $filename, 'public');
            
            // Return the full URL to the image
            return response()->json([
                'url' => Storage::url($path)
            ]);
        }

        return response()->json(['error' => 'Falha no upload.'], 400);
    }
}
