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
        return view('profile.editprofile', [
            'user'      => Auth::user(),
            'levels'    => getUserLevels(),
            'addresses' => Auth::user()->addresses
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
            'state'    => 'required|string|max:255',
            'country'     => 'required|string|max:255',
            'zip_code'  => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude'=> 'nullable|numeric',
            'complement' => 'nullable|string|max:255',
            'district'   => 'nullable|string|max:255',

        ]);

        Address::create([
            'user_id'   => Auth::user()->id,
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
    public function update(Request $request, $id)
{
    $address = Address::findOrFail($id);

    // Validação: idêntica ao método store
    $validated = $request->validate([
        'street'     => 'required|string|max:255',
        'number'     => 'required|string|max:20',
        'city'       => 'required|string|max:255',
        'state'      => 'required|string|max:255',
        'country'    => 'required|string|max:255',
        'zip_code'   => 'required|string|max:20',
        'latitude'   => 'nullable|numeric',
        'longitude'  => 'nullable|numeric',
        'complement' => 'nullable|string|max:255',
        'district'   => 'nullable|string|max:255',
    ]);

    // Atualiza registro
    $address->update([
        'street'     => $validated['street'],
        'number'     => $validated['number'],
        'complement' => $validated['complement'] ?? null,
        'district'   => $validated['district'] ?? null,
        'city'       => $validated['city'],
        'state'      => $validated['state'],
        'zip_code'   => $validated['zip_code'],
        'latitude'   => $validated['latitude'] ?? null,
        'longitude'  => $validated['longitude'] ?? null,
    ]);

    return redirect()
        ->back()
        ->with('success', 'Endereço atualizado com sucesso!');
}


    /**
     * Excluir endereço
     */
    public function destroy(Address $address)
    {
        $this->authorizeOwner($address);

        $address->delete();

        return redirect()->back()->with('msg', 'Endereço excluído com sucesso!');
    }

    /**
     * Garantir que o endereço pertence ao usuário logado
     */
    private function authorizeOwner(Address $address)
    {
        if ($address->user_id !== Auth::user()->id) {
            abort(403, 'Acesso negado.');
        }
    }

    public function setPrimary(Address $address)
    {
        $this->authorizeOwner($address);

        Address::where('user_id', Auth::user()->id)->update(['is_primary' => false]);

        $address->update(['is_primary' => true]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Endereço principal selecionado com sucesso'
        ]);


    }



}
