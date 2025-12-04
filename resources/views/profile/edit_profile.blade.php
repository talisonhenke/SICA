@extends('layouts.main')

@include('includes.toast')

@section('content')
    <style>
        :root {
            --primary-color: #4CAF50;
            --primary-dark: #2E7D32;
            --accent-color: #FFC107;
            --danger-color: #dc3545;
            --bg-light: #f5f7fa;
            --bg-card: #ffffff;
            --text-dark: #2f2f2f;
            --text-muted: #6c757d;
            --border-color: #e0e0e0;
        }

        body {
            background: var(--bg-light);
        }

        .profile-wrapper {
            max-width: 850px;
            margin: auto;
        }

        .profile-header-card {
            background: var(--bg-card);
            border-radius: 14px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .profile-photo {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 36px;
            font-weight: 700;
            margin-right: 20px;
        }

        .card-custom {
            background: var(--bg-card);
            border-radius: 14px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.2rem;
        }

        /* ENDEREÇOS */
        .address-entry {
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            background: var(--bg-light);
            transition: 0.2s;
        }

        .primary-card {
            background: #e8f5e9;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.07);
        }
    </style>

    <div class="container py-4">
        <div class="profile-wrapper">

            <!-- HEADER -->
            <!-- HEADER -->
            <div class="profile-header-card d-flex align-items-center justify-content-between">

                <!-- Foto / Ícone -->
                <div class="d-flex align-items-center gap-3">

                    <div class="profile-photo">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div class="profile-info">
                        <h3 class="mb-0">{{ $user->name }}</h3>

                        <p class="text-muted mb-0">{{ $user->email }}</p>

                        @if ($user->phone_number)
                            <p class="text-muted mb-0">
                                {{ formatPhone($user->phone_number) }}
                            </p>
                        @else
                            <p class="text-muted mb-0">Telefone não informado</p>
                        @endif

                        <span class="badge-level mt-1 d-inline-block">
                            {{ $levels[$user->user_lvl] ?? $user->user_lvl }}
                        </span>
                    </div>

                </div>

                <!-- Botão -->
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileOptions">
                        Editar Perfil
                    </button>
                </div>

            </div>


            @php
                function formatPhone($phone)
                {
                    if (!$phone) {
                        return null;
                    }

                    // Remove tudo que não for número
                    $digits = preg_replace('/\D/', '', $phone);

                    if (strlen($digits) !== 11) {
                        return $phone; // Se não tiver 11 dígitos, retorna como está
                    }

                    return sprintf(
                        '(%s) %s %s-%s',
                        substr($digits, 0, 2),
                        substr($digits, 2, 1),
                        substr($digits, 3, 4),
                        substr($digits, 7, 4),
                    );
                }
            @endphp

            <!-- MODAL EDITAR PERFIL -->
            <div class="modal fade" id="editProfileOptions" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Editar Perfil</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body text-center">

                            <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal"
                                data-bs-target="#editNameModal" data-bs-dismiss="modal">
                                Alterar Nome
                            </button>

                            <button class="btn btn-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#editPhoneModal"
                                data-bs-dismiss="modal">
                                Alterar Telefone
                            </button>

                            <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#editPasswordModal"
                                data-bs-dismiss="modal">
                                Alterar Senha
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ENDEREÇOS -->
            <div class="card-custom">
                <div class="section-title text-center">Meus Endereços</div>

                <div id="address-list">

                    @forelse($addresses as $address)
                        <div class="address-entry {{ $address->is_primary ? 'primary-card' : '' }}"
                            data-id="{{ $address->id }}">

                            <div class="d-flex justify-content-between align-items-start">

                                <div>
                                    <strong>{{ $address->street }}, Nº {{ $address->number }}</strong><br>
                                    {{ $address->city }} - {{ $address->state }}<br>
                                    CEP: {{ $address->zip_code }}
                                </div>

                                <div class="text-end">
                                    <label class="form-check form-switch m-0">
                                        <input type="checkbox" class="form-check-input primary-toggle"
                                            onclick="setPrimaryToggle(event, {{ $address->id }})"
                                            {{ $address->is_primary ? 'checked' : '' }}>
                                        <small class="text-muted ms-1">Principal</small>
                                    </label>
                                </div>

                            </div>

                            <div class="mt-2">
                                <button class="btn btn-primary edit-address-btn" data-bs-toggle="modal"
                                    data-bs-target="#editAddressModal" data-id="{{ $address->id }}"
                                    data-zip_code="{{ $address->zip_code }}" data-street="{{ $address->street }}"
                                    data-complement="{{ $address->complement }}" data-number="{{ $address->number }}"
                                    data-district="{{ $address->district }}" data-city="{{ $address->city }}"
                                    data-state="{{ $address->state }}" data-country="{{ $address->country }}" data-latitude="{{ $address->latitude }}" data-longitude="{{ $address->longitude }}">
                                    Editar
                                </button>


                                <form action="{{ route('addresses.destroy', $address->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Tem certeza que deseja excluir este endereço?');">
                                        Excluir
                                    </button>
                                </form>
                            </div>

                        </div>
                    @empty
                        <p class="text-muted text-center">Nenhum endereço cadastrado.</p>
                    @endforelse

                </div>

                <div class="d-grid mt-3">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#googleAddressModal">
                        + Adicionar Endereço
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('includes.edit_name_modal')
    @include('includes.edit_password_modal')
    @include('includes.edit_phone_modal')
    @include('includes.google_address_modal')
    @include('includes.google_address_edit_modal')

    <script>
        /* Define como endereço principal */
        async function setPrimaryToggle(event, id) {
            event.preventDefault();

            const el = event.target;
            const toggles = [...document.querySelectorAll('.primary-toggle')];
            const prev = toggles.map(t => t.checked);

            toggles.forEach(t => t.disabled = true);

            try {
                const resp = await fetch(`/addresses/${id}/primary`, {
                    method: "PATCH",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({})
                });

                if (!resp.ok) throw new Error("Erro HTTP " + resp.status);

                const data = await resp.json();

                toggles.forEach(t => t.checked = false);
                el.checked = true;

                if (data.status === "ok") {
                    showSessionToast(data.message);
                    reorderPrimaryCard(el);
                }

            } catch (err) {
                console.error(err);
                toggles.forEach((t, i) => t.checked = prev[i]);
                alert("Não foi possível definir como principal.");
            } finally {
                toggles.forEach(t => t.disabled = false);
            }
        }

        function reorderPrimaryCard(el) {
            const card = el.closest(".address-entry");
            const container = document.querySelector("#address-list");

            document.querySelectorAll('.address-entry').forEach(c =>
                c.classList.remove('primary-card')
            );

            card.classList.add('primary-card');
            container.prepend(card);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.edit-address-btn').forEach(btn => {
                btn.addEventListener('click', () => {

                    // Dados do botão
                    const id = btn.dataset.id;

                    // Atualiza a rota dinamicamente
                    const form = document.getElementById('editAddressForm');
                    form.action = `/addresses/${id}`; // rota addresses.update

                    // Preenche os campos
                    document.getElementById('zip_code_edit').value = btn.dataset.zip_code;
                    document.getElementById('street_edit').value = btn.dataset.street;
                    document.getElementById('number_edit').value = btn.dataset.number;
                    document.getElementById('complement_edit').value = btn.dataset.complement;
                    document.getElementById('district_edit').value = btn.dataset.district;
                    document.getElementById('city_edit').value = btn.dataset.city;
                    document.getElementById('state_edit').value = btn.dataset.state;
                    document.getElementById('country_edit').value = btn.dataset.country;
                    document.getElementById('latitude_edit').value = btn.dataset.latitude;
                    document.getElementById('longitude_edit').value = btn.dataset.longitude;
                });
            });
        });
    </script>
@endsection
