<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('owner.users.index', compact('users'));
    }

    public function create()
    {
        return view('owner.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:owner,admin,kasir,penerima,operator_cetak'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('owner.users.index')->with('success', 'Akun pengguna berhasil dibuat');
    }

    public function edit(User $user)
    {
        return view('owner.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:owner,admin,kasir,penerima,operator_cetak',
            'password' => 'nullable|min:6'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('owner.users.index')->with('success', 'Akun pengguna berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the only owner
        if ($user->role === 'owner' && User::where('role', 'owner')->count() === 1) {
            return back()->with('error', 'Tidak bisa menghapus satu-satunya owner');
        }

        // Set changed_by to NULL for related order status logs instead of deleting them
        \App\Models\OrderStatusLog::where('changed_by', $user->id)->update(['changed_by' => null]);
        // Set created_by to NULL for related orders (preserve data)
        \App\Models\Order::where('created_by', $user->id)->update(['created_by' => null]);
        // Set paid_by to NULL for related payments (preserve data)
        \App\Models\Payment::where('paid_by', $user->id)->update(['paid_by' => null]);
        $user->delete();

        return redirect()->route('owner.users.index')->with('success', 'Akun pengguna berhasil dihapus');
    }
}
