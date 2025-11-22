<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">Finalizar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">

                {{-- Formas de Pagamento --}}
                <h6 class="fw-bold">Forma de Pagamento</h6>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="radio" name="payment_method" id="paymentPix" value="pix" checked>
                    <label class="form-check-label" for="paymentPix">
                        PIX (única opção disponível no momento)
                    </label>
                </div>


                {{-- Endereços --}}
                <h6 class="fw-bold mt-3">Selecione o Endereço de Entrega</h6>

                @php
                    if (!Auth::check()) {
                        // Redireciona imediatamente se não estiver logado
                        header("Location: " . route('login'));
                        exit;
                    }

                    // Usuário autenticado → carregar endereços
                    $addresses = Auth::user()->addresses;
                    $hasAddresses = $addresses->count() > 0;
                @endphp


                @if ($hasAddresses)
                    <div class="list-group mb-3">

                        @foreach ($addresses as $address)
                            <label class="list-group-item d-flex align-items-start">

                                <input
                                    class="form-check-input me-3"
                                    type="radio"
                                    name="selected_address"
                                    value="{{ $address->id }}"
                                    {{ $address->is_primary ? 'checked' : '' }}
                                >

                                <div>
                                    <strong>
                                        {{ $address->street }},
                                        {{ $address->number }}
                                    </strong>
                                    <br>
                                    {{ $address->district }} — {{ $address->city }}<br>
                                    <span class="text-muted small">
                                        CEP: {{ $address->zipcode }}
                                    </span>

                                    @if ($address->is_primary)
                                        <span class="badge bg-primary ms-2">Principal</span>
                                    @endif
                                </div>

                            </label>
                        @endforeach

                    </div>
                @else
                    <div class="alert alert-warning">
                        Você ainda não possui endereços cadastrados.  
                        Use o formulário abaixo para adicionar seu endereço de entrega.
                    </div>
                @endif


                {{-- Botão para adicionar novo endereço --}}
                <button type="button" class="btn btn-outline-primary w-100 mb-3" data-bs-toggle="collapse" data-bs-target="#newAddressForm">
                    + Adicionar novo endereço
                </button>


                {{-- Formulário de novo endereço --}}
                <div class="collapse" id="newAddressForm">

                    <div class="card card-body">

                        <h6 class="fw-bold">Novo Endereço</h6>

                        <div class="mb-2">
                            <label class="form-label">Rua</label>
                            <input type="text" class="form-control" id="new_street">
                        </div>

                        <div class="row">
                            <div class="col-4 mb-2">
                                <label class="form-label">Número</label>
                                <input type="text" class="form-control" id="new_number">
                            </div>
                            <div class="col-8 mb-2">
                                <label class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="new_district">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-8 mb-2">
                                <label class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="new_city">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="form-label">CEP</label>
                                <input type="text" class="form-control" id="new_zipcode">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Observação (opcional)</label>
                            <input type="text" class="form-control" id="new_note">
                        </div>

                        <button type="button" class="btn btn-primary w-100 mt-2" id="btnSaveAddress">
                            Salvar Endereço
                        </button>

                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-success">
                    Confirmar e Continuar
                </button>
            </div>

        </div>
    </div>
</div>
