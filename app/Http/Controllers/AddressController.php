<?php

namespace App\Http\Controllers;

use App\Models\NewAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Tampilkan daftar alamat milik user.
     */
    public function index()
    {
        $addresses = NewAddress::where('user_id', Auth::id())->get();
        return view('user.address', compact('addresses'));
    }

    /**
     * Tampilkan form tambah alamat.
     */
    public function create()
    {
        return view('user.address-add');
    }

    /**
     * Simpan alamat baru.
     */
    public function store(Request $request)
    {

        // dd($request->all());

        $data = $request->validate([
            'destination_id'    => 'required|integer',
            'province_name'     => 'required|string|max:100',
            'city_name'         => 'required|string|max:100',
            'district_name'     => 'required|string|max:100',
            'subdistrict_name'  => 'required|string|max:100',
            'full_address'      => 'required|string|max:500',
            'zip_code'          => 'required|string|max:10',
            'phone_number'      => 'required|string|max:20',
            'recipient_name'    => 'required|string|max:100',
        ]);

        NewAddress::create([
            'user_id'        => Auth::id(),
            'destination_id' => $data['destination_id'],
            'province'       => $data['province_name'],
            'city'           => $data['city_name'],
            'district'       => $data['district_name'],
            'subdistrict'    => $data['subdistrict_name'],
            'full_address'   => $data['full_address'],
            'zip_code'       => $data['zip_code'],
            'phone'          => $data['phone_number'],
            'recipient_name' => $data['recipient_name'],
        ]);

        return redirect()->route('user.address.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit alamat.
     */
    public function edit($id)
    {
        $address = NewAddress::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.address-edit', compact('address'));
    }

    /**
     * Proses simpan perubahan alamat.
     */
    public function update(Request $request, $id)
    {

        // dd($request->all());

        $address = NewAddress::where('user_id', Auth::id())
            ->findOrFail($id);

        $data = $request->validate([
            'destination_id'    => 'required|integer',
            'province_name'     => 'required|string|max:100',
            'city_name'         => 'required|string|max:100',
            'district_name'     => 'required|string|max:100',
            'subdistrict_name'  => 'required|string|max:100',
            'full_address'      => 'required|string|max:500',
            'zip_code'          => 'required|string|max:10',
            'phone_number'      => 'required|string|max:20',
            'recipient_name'    => 'required|string|max:100',
        ]);

        $address->update([
            'destination_id' => $data['destination_id'],
            'province'       => $data['province_name'],
            'city'           => $data['city_name'],
            'district'       => $data['district_name'],
            'subdistrict'    => $data['subdistrict_name'],
            'full_address'   => $data['full_address'],
            'zip_code'       => $data['zip_code'],
            'phone'          => $data['phone_number'],
            'recipient_name' => $data['recipient_name'],
        ]);

        return redirect()->route('user.address.index')
            ->with('success', 'Alamat berhasil diubah.');
    }

    /**
     * Hapus alamat.
     */
    public function destroy($id)
    {
        $address = NewAddress::where('user_id', Auth::id())
            ->findOrFail($id);
        $address->delete();

        return redirect()->route('user.address.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }
}
