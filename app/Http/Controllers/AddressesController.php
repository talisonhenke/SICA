<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressesController extends Controller
{
    /**
     * Lista endereços do usuário logado
     */
    public function index()
    {
        $addresses = Auth::user()->addresses;

        return view('profile.editprofile', [
            'user'      => Auth::user(),
            'levels'    => getUserLevels(), // caso você use isso
            'addresses' => $addresses
        ]);
    }

    /**
     * Salvar novo endereço
     */
    public function store(Request $request)
    {
        $request->validate([
            'street'   => 'required|string|max:255',
            'number'   => 'required|string|max:20',
            'city'     => 'required|string|max:255',
            'state'    => 'required|string|max:2',
            'zip_code'  => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude'=> 'nullable|numeric',
            'complement' => 'nullable|string|max:255',
            'district'   => 'nullable|string|max:255',

        ]);

        Address::create([
            'user_id'   => Auth::id(),
            'street'    => $request->street,
            'number'    => $request->number,
            'complement'=> $request->complement,
            'district'  => $request->district,
            'city'      => $request->city,
            'state'     => $request->state,
            'zip_code'  => $request->zip_code,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'is_primary'=> false,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Endereço adicionado com sucesso!');
    }

    /**
     * Tela de edição (se você quiser rotas isoladas)
     */
    public function edit(Address $address)
    {
        $this->authorizeOwner($address);

        return view('addresses.edit', compact('address'));
    }

    /**
     * Atualizar endereço
     */
    public function update(Request $request, Address $address)
    {
        $this->authorizeOwner($address);

        $validated = $request->validate([
            'street'     => 'required|string|max:255',
            'number'     => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'district'   => 'nullable|string|max:255',
            'city'       => 'required|string|max:255',
            'state'      => 'required|string|max:2',
            'zip_code'   => 'required|string|max:20',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
        ]);

        $address->update($validated);

        return redirect()->back()->with('success', 'Endereço atualizado!');
    }

    /**
     * Excluir endereço
     */
    public function destroy(Address $address)
    {
        $this->authorizeOwner($address);

        $address->delete();

        return redirect()->back()->with('success', 'Endereço excluído!');
    }

    /**
     * Garantir que o endereço pertence ao usuário logado
     */
    private function authorizeOwner(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }
    }
}
