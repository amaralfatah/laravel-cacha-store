<?php

namespace App\Http\Controllers;

use App\Models\PrinterSetting;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PrinterSettingController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            // Admin dapat melihat pengaturan printer untuk semua toko
            $settings = PrinterSetting::with('store')->get();
            return view('settings.printer-admin', compact('settings'));
        } else {
            // User biasa hanya melihat pengaturan printer tokonya
            $setting = PrinterSetting::where('store_id', auth()->user()->store_id)->first();
            return view('settings.printer', compact('setting'));
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : '',
            'paper_size' => 'required|in:57mm,80mm',
            'printer_name' => 'nullable|string|max:255',
            'auto_print' => 'boolean'
        ]);

        // Tentukan store_id berdasarkan role
        $store_id = auth()->user()->role === 'admin'
            ? $request->store_id
            : auth()->user()->store_id;

        PrinterSetting::updateOrCreate(
            ['store_id' => $store_id],
            $request->only(['paper_size', 'printer_name', 'auto_print'])
        );

        return redirect()->back()->with('success', 'Pengaturan printer berhasil disimpan');
    }

    public function testPrint(Request $request)
    {
        // Ambil store_id berdasarkan role
        $store_id = auth()->user()->role === 'admin'
            ? $request->store_id
            : auth()->user()->store_id;

        // Ambil pengaturan printer
        $setting = PrinterSetting::where('store_id', $store_id)->first();

        // Jika setting belum ada, buat default
        if (!$setting) {
            $setting = new PrinterSetting([
                'store_id' => $store_id,
                'paper_size' => '80mm',
                'auto_print' => true
            ]);
        }

        // Ambil data toko
        $store = Store::find($store_id);

        $data = [
            'setting' => $setting,
            'store' => $store,
            'test_time' => now()
        ];

        return view('settings.printer-test', $data);
    }
}
