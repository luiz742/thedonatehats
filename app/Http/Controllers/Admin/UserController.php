<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::select('id', 'name', 'email', 'is_admin')->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users
        ]);
    }

    public function edit(User $user): Response
    {
        $user = User::with('kyc')->findOrFail($user->id);
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user
        ]);
    }

    public function updateKycStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if (!$user->kyc) {
            return redirect()->back()->with('error', 'KYC não encontrado para este usuário.');
        }

        $user->kyc->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status do KYC atualizado com sucesso!');
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'is_admin' => 'required|boolean'
        ]);

        dd($request->all());

        $user->update($request->all());

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }
}
