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
        $user = Auth::guard('users')->user();
        $apartments = Apartment::with('rents', 'expenses')
        ->where('owner_id', $user->id)
        ->paginate($request->get('per_page', 50));

    $total_total_amount = 0;
    $total_expense_amount = 0;
    $total_rent_amount = 0;

    foreach ($apartments as $apartment) {
        $expense_amount = $apartment->expenses->sum('amount');
        $rent_amount = $apartment->rents->sum('amount');
        $total_amount = $rent_amount - $expense_amount;
        $apartment->total_amount = $total_amount;

        $total_total_amount += $total_amount;
        $total_expense_amount += $expense_amount;
        $total_rent_amount += $rent_amount;
    }

    $data = [
        'apartments' => $apartments,
        'total_total_amount' => $total_total_amount,
        'total_expense_amount' => $total_expense_amount,
        'total_rent_amount' => $total_rent_amount,
    ];

        return response()->json($data, 200);
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
            'apartment_name' => $request->apartment_name,
            'apartment_number' => $request->apartment_number,
            'owner_id' => Auth::user()->id,
            'apartment_address' => $request->apartment_address,
            'owner_phone' => $request->owner_phone,
            'photo' => $photo,
        ]);

        return response()->json($apartment, 200);
    }

    public function show($id)
    {
        $apartment = Apartment::findOrFail($id);
        return response()->json($apartment, 200);
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
            'apartment_name' => $request->apartment_name,
            'apartment_number' => $request->apartment_number,
            'owner_id' => Auth::guard('users')->user()->id,
            'apartment_address' => $request->apartment_address,
            'owner_phone' => $request->owner_phone,
            'photo' => $photo,
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
