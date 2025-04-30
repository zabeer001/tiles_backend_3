<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class HelperMethods
{
    public static function uploadImage($image)
    {
        try {
            // Check if the image is valid
            if ($image && $image->isValid()) {
                // Define the destination path (public/tiles folder)
                $destinationPath = public_path('uploads');

                // Generate a unique filename for the image (optional)
                $imageName = time() . '_' . $image->getClientOriginalName();

                // Move the image to the tiles folder
                $image->move($destinationPath, $imageName);

                // Return the relative path to the image
                return 'uploads/' . $imageName;
            }

            // Return null if no image is uploaded or it is invalid
            return null;
        } catch (\Exception $e) {
            // Log the error if something goes wrong
            Log::error('Image upload failed: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString(),
            ]);

            // Return null in case of an error
            return null;
        }
    }

    public static function updateImage(Request $request, $obj)
    {
        // Check if a new image is uploaded and delete the previous image if it exists
        if ($request->hasFile('image')) {
            // Delete the previous image if it exists
            if ($obj->image && file_exists(public_path($obj->image))) {
                unlink(public_path($obj->image)); // Delete the old image
            }

            // Handle image upload using the helper function
            return HelperMethods::uploadImage($request->file('image'));
        }

        // If no new image is uploaded, keep the old image
        return $obj->image;
    }

    
}