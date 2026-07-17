<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $empresaId = $request->user()->empresa_id;

        return view('usuarios.index', [
            'usuarios' => User::query()
                ->where('empresa_id', $empresaId)
                ->orderByDesc('is_active')
                ->orderBy('role')
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('usuarios.create', [
            'usuario' => new User(['is_active' => true, 'role' => UserRole::Gestor]),
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        User::create([
            ...$this->validated($request),
            'empresa_id' => $request->user()->empresa_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('usuarios.index')->with('status', 'Usuário cadastrado com sucesso.');
    }

    public function edit(Request $request, User $usuario): View
    {
        $this->authorizeUsuario($request, $usuario);

        return view('usuarios.edit', [
            'usuario' => $usuario,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $this->authorizeUsuario($request, $usuario);

        $data = [
            ...$this->validated($request, $usuario),
            'is_active' => $request->boolean('is_active'),
        ];

        if (! $request->filled('password')) {
            unset($data['password']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('status', 'Usuário atualizado com sucesso.');
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        $this->authorizeUsuario($request, $usuario);

        if ($usuario->is($request->user())) {
            return back()->with('status', 'Você não pode desativar seu próprio usuário.');
        }

        $usuario->update(['is_active' => false]);

        return redirect()->route('usuarios.index')->with('status', 'Usuário desativado com sucesso.');
    }

    private function validated(Request $request, ?User $usuario = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => [$usuario ? 'nullable' : 'required', 'string', 'min:6'],
        ]);
    }

    private function authorizeUsuario(Request $request, User $usuario): void
    {
        abort_unless((int) $usuario->empresa_id === (int) $request->user()->empresa_id, 403);
    }
}
