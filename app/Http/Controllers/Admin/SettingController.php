<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        if (!auth('admin')->user()->can('settings-index')) {
            abort(403);
        }

        $settings = DB::table('settings')->orderBy('created_at', 'desc')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'email_technical_issue' => 'required|email|max:255',
            'address_ar' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'google_map' => 'required|string',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = uploadImage('assets/admin/uploads', $request->logo);

        DB::table('settings')->insert([
            'phone' => $request->phone,
            'email' => $request->email,
            'email_technical_issue' => $request->email_technical_issue,
            'address_ar' => $request->address_ar,
            'address' => $request->address,
            'google_map' => $request->google_map,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'facebook' => $request->facebook,
            'logo' => $logoPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('settings.index')->with('success', __('messages.setting_created'));
    }

    public function edit($id)
    {
        $setting = DB::table('settings')->where('id', $id)->first();
        if (!$setting) {
            return redirect()->route('settings.index')->with('error', __('messages.setting_not_found'));
        }
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'email_technical_issue' => 'required|email|max:255',
            'address_ar' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'google_map' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $setting = DB::table('settings')->where('id', $id)->first();
        if (!$setting) {
            return redirect()->route('settings.index')->with('error', __('messages.setting_not_found'));
        }

        $updateData = [
            'phone' => $request->phone,
            'email' => $request->email,
            'email_technical_issue' => $request->email_technical_issue,
            'address_ar' => $request->address_ar,
            'address' => $request->address,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'facebook' => $request->facebook,
            'google_map' => $request->google_map,
            'updated_at' => now(),
        ];

        if ($request->hasFile('logo')) {
            $updateData['logo'] = uploadImage('assets/admin/uploads', $request->logo);
        }

        DB::table('settings')->where('id', $id)->update($updateData);

        return redirect()->route('settings.index')->with('success', __('messages.setting_updated'));
    }

    public function destroy($id)
    {
        DB::table('settings')->where('id', $id)->delete();
        return redirect()->route('settings.index')->with('success', __('messages.setting_deleted'));
    }
}