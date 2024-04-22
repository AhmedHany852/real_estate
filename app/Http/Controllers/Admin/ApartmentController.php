<?php

namespace App\Http\Controllers\Admin;

use App\Models\Apartment;
use App\Models\Expense;
use App\Models\Rent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $apartments = Apartment::with('rents','expenses')->paginate($request->get('per_page', 50));

        foreach ($apartments as $apartment) {
            $expense_amount = Expense::where('apartment_id', $apartment->id)->value('amount');
            $rent_amount = Rent::where('apartment_id', $apartment->id)->value('amount');

            // Initialize total amount with rent amount
            $total_amount = $rent_amount;

            // If there's an expense amount, subtract it from the total amount
            if ($expense_amount) {
                $total_amount -= $expense_amount;
            }

            // Assign the calculated total amount to the apartment object
            $apartment->total_amount = $total_amount;
        }

        $user = Auth::user();
        $apartments = Apartment::where('owner_id', $user->id)->paginate($request->get('per_page', 50));

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
            'owner_id' => Auth::user()->id,
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
            'owner_id' => Auth::user()->id,
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
