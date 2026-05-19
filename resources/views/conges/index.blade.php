<x-app-layout>
    <x-slot name="header">Demandes de congé</x-slot>

    <x-alert />

    @if(auth()->user()->isEmployee())
        {{-- Widget solde --}}
        @include('conges.partials.balance-widget')
        <div class="mb-4">
            <a href="{{ route('conges.create') }}"
               class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
                + Nouvelle demande
            </a>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    @if(auth()->user()->hasRole(['admin','rh']))
                        <th class="px-6 py-3 text-left">Employé</th>
                    @endif
                    <th class="px-6 py-3 text-left">Période</th>
                    <th class="px-6 py-3 text-left">Jours ouvrables</th>
                    <th class="px-6 py-3 text-left">Motif</th>
                    <th class="px-6 py-3 text-left">Statut</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($conges as $conge)
                    <tr class="hover:bg-gray-50">
                        @if(auth()->user()->hasRole(['admin','rh']))
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $conge->employee->full_name }}
                            </td>
                        @endif
                        <td class="px-6 py-4 text-gray-600">
                            {{ $conge->start_date->format('d/m/Y') }}
                            → {{ $conge->end_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $conge->working_days }} j</td>
                        <td class="px-6 py-4 text-gray-500">{{ $conge->reason ?? '—' }}</td>
                        <td class="px-6 py-4">
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
                            @if($conge->isRejected() && $conge->rejected_reason)
                                <p class="text-xs text-red-400 mt-1">{{ $conge->rejected_reason }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($conge->isPending())
                                @if(auth()->user()->hasRole(['admin','rh']))
                                    <form method="POST"
                                          action="{{ route('conges.approve', $conge) }}"
                                          class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-green-600 hover:underline text-xs">
                                            Approuver
                                        </button>
                                    </form>
                                    <button onclick="document.getElementById('reject-{{ $conge->id }}').classList.toggle('hidden')"
                                            class="text-red-500 hover:underline text-xs">
                                        Refuser
                                    </button>
                                    {{-- Formulaire refus inline --}}
                                    <form id="reject-{{ $conge->id }}"
                                          method="POST"
                                          action="{{ route('conges.reject', $conge) }}"
                                          class="hidden mt-2">
                                        @csrf @method('PATCH')
                                        <textarea name="rejected_reason" rows="2"
                                                  placeholder="Motif de refus..."
                                                  class="w-full border border-gray-300 rounded px-2 py-1 text-xs"
                                                  required></textarea>
                                        <button class="mt-1 bg-red-500 text-white px-3 py-1 rounded text-xs">
                                            Confirmer le refus
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                          action="{{ route('conges.cancel', $conge) }}"
                                          onsubmit="return confirm('Annuler cette demande ?')">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-400 hover:underline text-xs">Annuler</button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            Aucune demande de congé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $conges->links() }}
        </div>
    </div>
</x-app-layout>