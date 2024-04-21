<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $apartments = Apartment::paginate($request->get('per_page', 50));
        return response()->json($apartments, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apartment_name' => 'required',
            'apartment_number' => 'required',
            'owner_id' => 'required',
            'apartment_address' => 'required',
            'owner_phone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }
        if ($request->file('photo')) {
            $avatar = $request->file('photo');
            $avatar->store('uploads/apartment_photo/', 'public');
            $photo = $avatar->hashName();
        } else {
            $photo = null;
        }

        $apartment = Apartment::create([
            'apartment_name' =>$request->apartment_name ,
            'apartment_number' =>$request-> apartment_number,
            'owner_id' => $request->owner_id,
            'apartment_address' =>$request->apartment_address ,
            'owner_phone' =>$request->owner_phone ,
            'photo' =>$photo,
        ]);

        return response()->json($apartment, 200);
    }

    public function show($id)
    {
        $apartment = Apartment::findOrFail($id);
        return response()->json($apartment , 200);
    }

    public function update(Request $request, $id)
    {
        $apartment = Apartment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'apartment_name' => 'required',
            'apartment_number' => 'required',
            'owner_id' => 'required',
            'apartment_address' => 'required',
            'owner_phone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }
        if ($request->file('photo')) {
            $avatar = $request->file('photo');
            $avatar->store('uploads/apartment_photo/', 'public');
            $photo = $avatar->hashName();
        } else {
            $photo = null;
        }

        $apartment->update([
            'apartment_name' =>$request->apartment_name ,
            'apartment_number' =>$request->apartment_number,
            'owner_id' => $request->owner_id,
            'apartment_address' =>$request->apartment_address ,
            'owner_phone' =>$request->owner_phone,
            'photo' =>$photo,
        ]);

        return response()->json($apartment, 200);
    }

    public function destroy($id)
    {
        try {
            $apartment = Apartment::findOrFail($id);
    
            // Delete personal photo if it exists
            if ($apartment->apartment_photo) {
                // Assuming 'personal_photo' is the attribute storing the file name
                $photoPath = 'uploads/apartment_photo/' . $apartment->apartment_photo;
    
                // Delete photo from storage
                Storage::delete($photoPath);
            }
    
            $apartment->delete();
    
            return response()->json(['message' => 'تمت عملية الحذف بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء محاولة حذف الشقة'], 400);
        }
    }
    
}
