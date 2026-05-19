<x-app-layout>
    <x-slot name="header">Tableau de bord — Admin</x-slot>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat-card label="Employés actifs"
                     :value="$stats['total_employees']"
                     icon="users" color="blue" />
        <x-stat-card label="Congés en attente"
                     :value="$stats['pending_conges']"
                     icon="clock" color="yellow" />
        <x-stat-card label="Congés approuvés"
                     :value="$stats['approved_conges']"
                     icon="check-circle" color="green" />
        <x-stat-card label="Employés archivés"
                     :value="$stats['archived']"
                     icon="archive-box" color="gray" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Répartition par département --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Répartition par département</h3>
            @forelse($by_department as $dept)
                @php
                    $pct = $stats['total_employees'] > 0
                        ? round(($dept->total / $stats['total_employees']) * 100)
                        : 0;
                @endphp
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $dept->department }}</span>
                        <span class="text-gray-400">{{ $dept->total }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">Aucun département.</p>
            @endforelse
        </div>

        {{-- Dernières demandes de congé --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Dernières demandes de congé</h3>
                <a href="{{ route('conges.index') }}"
                   class="text-xs text-blue-600 hover:underline">Voir tout</a>
            </div>
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-400 uppercase border-b border-gray-100">
                    <tr>
                        <th class="pb-2 text-left">Employé</th>
                        <th class="pb-2 text-left">Période</th>
                        <th class="pb-2 text-left">Jours</th>
                        <th class="pb-2 text-left">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recent_conges as $conge)
                        <tr>
                            <td class="py-2 text-gray-800">{{ $conge->employee->full_name }}</td>
                            <td class="py-2 text-gray-500">
                                {{ $conge->start_date->format('d/m') }}
                                → {{ $conge->end_date->format('d/m/Y') }}
                            </td>
                            <td class="py-2 text-gray-500">{{ $conge->working_days }} j</td>
                            <td class="py-2">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-400">
                                Aucune demande.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Derniers employés ajoutés --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Derniers employés ajoutés</h3>
                <a href="{{ route('employees.index') }}"
                   class="text-xs text-blue-600 hover:underline">Voir tout</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                @foreach($recent_employees as $emp)
                    <a href="{{ route('employees.show', $emp) }}"
                       class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50 transition text-center">
                        @if($emp->avatar)
                            <img src="{{ Storage::url($emp->avatar) }}"
                                 class="w-10 h-10 rounded-full object-cover mb-2" alt="" />
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700
                                        flex items-center justify-center text-sm font-medium mb-2">
                                {{ strtoupper(substr($emp->first_name,0,1).substr($emp->last_name,0,1)) }}
                            </div>
                        @endif
                        <p class="text-xs font-medium text-gray-800">{{ $emp->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $emp->position }}</p>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
</x-app-layout>