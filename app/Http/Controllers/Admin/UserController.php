<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Models\Delivery;
use App\Models\Representative;

class UserController extends Controller
{


    public function search(Request $request)
    {
        $query = $request->input('q');
        $users = User::where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->get(['id', 'name']);

        return response()->json($users);
    }

    public function addresses($id)
    {
        $user = User::find($id);
        $addresses = $user->addresses()->get(['id', 'address','delivery_id']); // Adjust the fields based on your address model

        return response()->json($addresses);
    }

    public function getFee($deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);
        return response()->json(['price' => $delivery->price]);
    }

    public function index(Request $request)
    {
        if (!auth('admin')->user()->can('users-index')) {
            abort(403);
        }

        // Check if there's a search query
        if ($request->search) {
            $data = User::where('user_type', 1)
                        ->where(function ($q) use ($request) {
                            $q->where(\DB::raw('CONCAT_WS(" ", `name`, `email`, `phone`)'), 'like', '%' . $request->search . '%');
                        })
                        ->paginate(PAGINATION_COUNT);
        } else {
            $data = User::where('user_type', 1)
                        ->paginate(PAGINATION_COUNT);
        }

        $searchQuery = $request->search;

        return view('admin.customers.index', compact('data', 'searchQuery'));
    }



    public function export(Request $request)
    {
        return Excel::download(new UsersExport($request->search), 'users.xlsx');
    }


    public function show($id)
    {
        $data = User::where('user_type',1)->findOrFail($id);
        return view('admin.customers.show',compact('data'));
    }


    public function edit($id)
    {
        if (auth()->user()->can('customer-edit')) {
            $data = User::where('user_type',1)->findorFail($id);
            return view('admin.customers.edit', compact('data'));
        } else {
            return redirect()->back()
                ->with('error', "Access Denied");
        }
    }

       public function update(Request $request,$id)
       {
         $customer=User::where('user_type',1)->findorFail($id);
         try{

             $customer->name = $request->get('name');
             if($request->password){
                $customer->password = Hash::make($request->password);
             }
             $customer->email = $request->get('email');
             $customer->phone = $request->get('phone');
             $customer->can_pay_with_receivable = $request->get('can_pay_with_receivable');

             if($request->activate){
                $customer->activate = $request->get('activate');
            }
            if ($request->has('photo')) {
                $the_file_path = uploadImage('assets/admin/uploads', $request->photo);
                $customer->photo = $the_file_path;
             }
             if($customer->save()){
                 return redirect()->route('admin.customer.index')->with(['success' => 'Customer update']);

             }else{
                 return redirect()->back()->with(['error' => 'Something wrong']);
             }

         }catch(\Exception $ex){
             return redirect()->back()
             ->with(['error' => 'عفوا حدث خطأ ما' . $ex->getMessage()])
             ->withInput();
         }

      }



}
