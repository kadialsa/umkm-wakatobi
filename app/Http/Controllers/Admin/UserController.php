<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $ // 2) Bangun query: hanya USR & ADM
        $users = User::whereIn('utype', ['USR', 'ADM'])
            // 3) Jika ada cari, filter by name
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            // 4) Urutkan terbaru dulu
            ->orderBy('id', 'DESC')
            // 5) Paginate + pertahankan ?search=…
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }


    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile'   => ['required', 'string', 'max:15',  'unique:users,mobile'],
            'utype'    => ['required', Rule::in(['ADM', 'STR', 'USR'])],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // hash the password
        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // 1) validate all fields
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'mobile' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'mobile')->ignore($user->id),
            ],
            'utype'  => ['required', Rule::in(['ADM', 'STR', 'USR'])],
        ]);

        // 2) if password provided, validate & hash it
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
            $data['password'] = bcrypt($request->password);
        }

        // 3) update and redirect
        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User has been updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('status', 'User deleted');
    }
}
