<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::paginate($request->get('per_page', 50));
        return response()->json($expenses, 200);
    }
//     public function index(Request $request)
// {
//     // Retrieve all expenses along with the sum of expenses for each apartment
//     $expenses = Expense::select('expenses.*', 'apartments.apartment_name')
//                        ->leftJoin('apartments', 'expenses.apartment_id', '=', 'apartments.id')
//                        ->selectRaw('expenses.apartment_id, sum(expenses.amount) as total_amount')
//                        ->groupBy('expenses.apartment_id', 'apartments.apartment_name')
//                        ->paginate($request->get('per_page', 50));

//     return response()->json($expenses, 200);
// }

    


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'amount' => 'required',
            'description' => 'nullable',
            
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }
        $expenses = Expense::create([
            'description' =>$request->description ,
            'amount' =>$request-> amount,
            'apartment_id' => $request->apartment_id,           
        ]);

        return response()->json($expenses, 200);
    }

    public function show($id)
    {
        $expenses = Expense::findOrFail($id);
        return response()->json($expenses , 200);
    }

    public function update(Request $request, $id)
    {
        $expenses = Expense::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'amount' => 'required',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }
      

        $expenses->update([
            'description' =>$request->description ,
            'amount' =>$request-> amount,
            'apartment_id' => $request->apartment_id, 
        ]);

        return response()->json($expenses, 200);
    }

    public function destroy($id)
    {
        try {
            $expenses = Expense::findOrFail($id);
            $expenses->delete();
    
            return response()->json(['message' => 'تمت عملية الحذف بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء محاولة الحذف '], 400);
        }
    }
    
}
