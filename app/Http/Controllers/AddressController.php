<?php

namespace App\Http\Controllers;

use App\Models\Address;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = $userId = Auth::id(); // ambil ID user yang sedang login
        $addresses = Address::where('user_id', $userId)->get();
        return view('user.address', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $addresses = Address::all();
        return response()->json($addresses);
    }

    public function getByUser($userId)
    {
        $addresses = Address::where('user_id', $userId)->get();
        return response()->json($addresses);
    }


    // Ambil alamat default dari user
    public function getDefaultAddress($userId)
    {
        $address = Address::where('user_id', $userId)
            ->where('isdefault', true)
            ->first();

        return response()->json($address);
    }

    public function address_add()
    {

        return view('user.address-add');
    }
    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'zip' => 'required',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
            'country' => 'required', //  penting!
        ]);

        Address::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'zip' => $request->zip,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'locality' => $request->locality,
            'landmark' => $request->landmark,
            'country' => $request->country,
        ]);

        return redirect()->route('user.address')->with('success', 'Address added successfully.');
    }

    public function address_edit($id)
    {
        $address = Address::findOrFail($id);
        return view('user.address-edit', compact('address'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'zip' => 'required',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
            'country' => 'required',
        ]);

        $address = Address::findOrFail($id);
        $address->update($request->all());

        return redirect()->route('user.address')->with('success', 'Address updated successfully.');
    }

    public function destroy($id)
    {
        $address = Address::findOrFail($id);

        // Pastikan alamat milik user yang sedang login
        if ($address->user_id !== Auth::id()) {
            return redirect()->route('user.address')->with('error', 'Unauthorized action.');
        }

        $address->delete();

        return redirect()->route('user.address')->with('success', 'Address deleted successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    // public function update(UpdateAddressRequest $request, Address $address)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
}

  // $userId = Auth::id(); // ambil ID user yang sedang login
        // $data = $request->validated(); // ambil data yang sudah divalidasi dari StoreAddressRequest

        // // Set user_id
        // $data['user_id'] = $userId;

        // // Jika isdefault = true, reset alamat default lain milik user
        // if (isset($data['isdefault']) && $data['isdefault']) {
        //     Address::where('user_id', $userId)->update(['isdefault' => false]);
        // }

        // // Simpan ke database
        // Address::create($data);
