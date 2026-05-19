<x-app-layout>
    <x-slot name="header">Tableau de bord — RH</x-slot>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat-card label="Employés actifs"
                     :value="$stats['total_employees']"
                     icon="users" color="blue" />
        <x-stat-card label="En attente"
                     :value="$stats['pending_conges']"
                     icon="clock" color="yellow" />
        <x-stat-card label="Approuvés ce mois"
                     :value="$stats['approved_conges']"
                     icon="check-circle" color="green" />
        <x-stat-card label="Refusés ce mois"
                     :value="$stats['rejected_conges']"
                     icon="x-circle" color="red" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Demandes en attente (action requise) --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">
                Demandes en attente
                @if($stats['pending_conges'] > 0)
                    <span class="ml-2 bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">
                        {{ $stats['pending_conges'] }}
                    </span>
                @endif
            </h3>

            @forelse($pending_conges as $conge)
                <div class="flex items-start justify-between py-3 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700
                                    flex items-center justify-center text-xs font-medium shrink-0">
                            {{ strtoupper(substr($conge->employee->first_name,0,1).substr($conge->employee->last_name,0,1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $conge->employee->full_name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $conge->start_date->format('d/m/Y') }}
                                → {{ $conge->end_date->format('d/m/Y') }}
                                · {{ $conge->working_days }} j ouvrable(s)
                            </p>
                            @if($conge->reason)
                                <p class="text-xs text-gray-400 italic">« {{ $conge->reason }} »</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0 ml-4">
                        <form method="POST" action="{{ route('conges.approve', $conge) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs bg-green-100 text-green-700 hover:bg-green-200
                                           px-3 py-1.5 rounded-lg font-medium transition">
                                Approuver
                            </button>
                        </form>
                        <a href="{{ route('conges.index') }}"
                           class="text-xs bg-gray-100 text-gray-600 hover:bg-gray-200
                                  px-3 py-1.5 rounded-lg font-medium transition">
                            Détails
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-sm text-gray-400">Aucune demande en attente. 🎉</p>
                </div>
            @endforelse
        </div>

        {{-- Répartition par département --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Par département</h3>
            @forelse($by_department as $dept)
                @php
                    $pct = $stats['total_employees'] > 0
                        ? round(($dept->total / $stats['total_employees']) * 100)
                        : 0;
                @endphp
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $dept->department }}</span>
                        <span class="text-gray-400">{{ $dept->total }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">Aucune donnée.</p>
            @endforelse
        </div>

    </div>

    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-semibold text-gray-700">Employes par departement</h3>
            <a href="{{ route('employees.index') }}"
               class="text-xs text-blue-600 hover:underline">Voir tout</a>
        </div>

        <div class="space-y-6">
            @forelse($employees_by_department as $department => $employees)
                <section>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-3">
                        <h4 class="text-sm font-semibold text-gray-800">{{ $department }}</h4>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                            {{ $employees->count() }} employe(s)
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($employees as $employee)
                            <a href="{{ route('employees.show', $employee) }}"
                               class="flex items-center gap-3 rounded-lg border border-gray-100 px-4 py-3 hover:bg-gray-50 transition">
                                @if($employee->avatar)
                                    <img src="{{ Storage::url($employee->avatar) }}"
                                         class="w-10 h-10 rounded-full object-cover" alt="" />
                                @else
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700
                                                flex items-center justify-center text-sm font-semibold shrink-0">
                                        {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $employee->full_name }}
                                    </p>
                                    <p class="text-xs text-gray-400 truncate">{{ $employee->position }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $employee->email }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">Aucun employe trouve.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
