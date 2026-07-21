<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GestorController extends Controller
{
    public function index(Request $request): View
    {
        $empresaId = $request->user()->empresa_id;

        return view('gestores.index', [
            'gestores' => User::where('empresa_id', $empresaId)
                ->where('role', UserRole::Gestor)
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('gestores.create', ['gestor' => new User(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $email = $request->string('email')->lower()->toString();
        $empresaId = $request->user()->empresa_id;

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if ((int) $existingUser->empresa_id === (int) $empresaId && ! $existingUser->is($request->user())) {
                $existingUser->update([
                    ...$data,
                    'empresa_id' => $empresaId,
                    'email' => $email,
                    'role' => UserRole::Gestor,
                    'is_active' => $request->boolean('is_active', true),
                ]);

                return redirect()->route('gestores.index')->with('status', 'Gestor cadastrado com sucesso.');
            }

            return back()
                ->withErrors(['email' => 'Este e-mail ja esta em uso por outro usuario.'])
                ->withInput($request->except('password'));
        }

        User::create([
            ...$data,
            'email' => $email,
            'empresa_id' => $request->user()->empresa_id,
            'role' => UserRole::Gestor,
            'is_active' => $request->boolean('is_active', true),
            'password' => $request->string('password')->toString() ?: 'password',
        ]);

        return redirect()->route('gestores.index')->with('status', 'Gestor cadastrado com sucesso.');
    }

    public function edit(Request $request, User $gestor): View
    {
        $this->authorizeGestor($request, $gestor);

        return view('gestores.edit', compact('gestor'));
    }

    public function update(Request $request, User $gestor): RedirectResponse
    {
        $this->authorizeGestor($request, $gestor);

        $data = [
            ...$this->validated($request, $gestor),
            'is_active' => $request->boolean('is_active'),
        ];

        if (! $request->filled('password')) {
            unset($data['password']);
        }

        $gestor->update($data);

        return redirect()->route('gestores.index')->with('status', 'Gestor atualizado com sucesso.');
    }

    public function destroy(Request $request, User $gestor): RedirectResponse
    {
        $this->authorizeGestor($request, $gestor);
        $gestor->update(['is_active' => false]);

        return redirect()->route('gestores.index')->with('status', 'Gestor desativado com sucesso.');
    }

    private function validated(Request $request, ?User $gestor = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                ...($gestor ? [Rule::unique('users', 'email')->ignore($gestor)] : []),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => [$gestor ? 'nullable' : 'required', 'string', 'min:6'],
        ]);
    }

    private function authorizeGestor(Request $request, User $gestor): void
    {
        abort_unless(
            $gestor->empresa_id === $request->user()->empresa_id && $gestor->role === UserRole::Gestor,
            403,
        );
    }
}
