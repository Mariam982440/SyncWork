<x-app-layout>
    <x-slot name="header">Bonjour, {{ $employee->first_name }} </x-slot>

    {{-- Alerte carence --}}
    @if(!$is_eligible)
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4 flex gap-3">
            <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-yellow-800">Période de carence en cours</p>
                <p class="text-xs text-yellow-600 mt-0.5">
                    Vous pourrez poser des congés à partir du
                    <strong>{{ $eligible_from->format('d/m/Y') }}</strong>.
                </p>
            </div>
        </div>
    @endif

    {{-- Solde congés --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Jours acquis</p>
            <p class="text-3xl font-bold text-gray-800">
                {{ $balance?->accrued_days ?? 0 }}
                <span class="text-base font-normal text-gray-400">j</span>
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Jours utilisés</p>
            <p class="text-3xl font-bold text-gray-800">
                {{ $balance?->used_days ?? 0 }}
                <span class="text-base font-normal text-gray-400">j</span>
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Disponibles</p>
            <p class="text-3xl font-bold {{ ($balance?->available_days ?? 0) > 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ $balance?->available_days ?? 0 }}
                <span class="text-base font-normal text-gray-400">j</span>
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Prochain crédit : {{ $next_accrual }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Fiche rapide --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-4 mb-5">
                @if($employee->avatar)
                    <img src="{{ Storage::url($employee->avatar) }}"
                         class="w-14 h-14 rounded-full object-cover" alt="" />
                @else
                    <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-700
                                flex items-center justify-center text-lg font-semibold">
                        {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
                    </div>
                @endif
                <div>
                    <p class="font-semibold text-gray-800">{{ $employee->full_name }}</p>
                    <p class="text-sm text-gray-400">{{ $employee->position }}</p>
                </div>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Département</dt>
                    <dd class="text-gray-700 font-medium">{{ $employee->department }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Embauche</dt>
                    <dd class="text-gray-700 font-medium">{{ $employee->hire_date->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">E-mail</dt>
                    <dd class="text-gray-700 font-medium">{{ $employee->email }}</dd>
                </div>
            </dl>
        </div>

        {{-- Historique congés --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Mes dernières demandes</h3>
                <div class="flex gap-2">
                    @if($is_eligible)
                        <a href="{{ route('conges.create') }}"
                           class="text-xs bg-blue-700 text-white px-3 py-1.5 rounded-lg hover:bg-blue-800">
                            + Nouvelle demande
                        </a>
                    @endif
                    <a href="{{ route('conges.index') }}"
                       class="text-xs text-blue-600 hover:underline self-center">Voir tout</a>
                </div>
            </div>

            @forelse($conges as $conge)
                <div class="flex items-center justify-between py-3
                            border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm text-gray-700">
                            {{ $conge->start_date->format('d/m/Y') }}
                            → {{ $conge->end_date->format('d/m/Y') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $conge->working_days }} jour(s) ouvrable(s)
                            @if($conge->reason) · {{ $conge->reason }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge type="{{ match($conge->status) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'warning',
                        } }}">
                            {{ match($conge->status) {
                                'pending'  => 'En attente',
                                'approved' => 'Approuvée',
                                'rejected' => 'Refusée',
                            } }}
                        </x-badge>
                        @if($conge->isPending())
                            <form method="POST" action="{{ route('conges.cancel', $conge) }}"
                                  onsubmit="return confirm('Annuler cette demande ?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-gray-400 hover:text-red-500">Annuler</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-4 text-center">
                    Aucune demande pour le moment.
                </p>
            @endforelse
        </div>

    </div>
</x-app-layout>