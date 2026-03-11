<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Employees List') }}
            </h2>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                {{ __('Add new employee') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('employees.list') }}" class="flex flex-wrap items-center gap-3">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="{{ __('Search employees, department, status...') }}"
                class="w-full max-w-md rounded-md border-slate-300 text-sm"
            />
            <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                {{ __('Search') }}
            </button>
            @if (!empty($search))
                <a href="{{ route('employees.list') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('Clear') }}</a>
            @endif
        </form>

        <div
            class="rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm"
            data-manager-update-url-template="{{ route('employees.manager.update', ['employee' => '__EMPLOYEE__']) }}"
        >
            <h3 class="text-base font-semibold text-slate-900">{{ __('Organization Structure') }}</h3>
            <p class="mt-1 text-sm text-slate-600">{{ __('Drag an employee card and drop it on another employee to assign a new manager.') }}</p>
            <div
                class="org-top-drop mt-3 rounded-lg border border-dashed border-slate-300 bg-white px-3 py-2 text-xs text-slate-600 transition"
                data-org-drop-target="1"
                data-manager-id=""
                data-manager-name="{{ __('No manager (top level)') }}"
            >
                {{ __('Drop here to move employee to top level (no manager).') }}
            </div>

            @if ($organizationTree->isNotEmpty())
                <ul class="mt-4 space-y-3">
                    @foreach ($organizationTree as $node)
                        @include('employees.partials.org-node', ['node' => $node])
                    @endforeach
                </ul>
            @else
                <p class="mt-3 text-sm text-slate-500">{{ __('No employees found to build the organization structure.') }}</p>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('Employee #') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('User') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Position') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Department') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $employee)
                        <tr>
                            <td class="px-4 py-3">{{ $employee->employee_number ?: '-' }}</td>
                            <td class="px-4 py-3">
                                {{ optional($employee->user)->name ?? '-' }}
                            </td>                            
                            <td class="px-4 py-3">{{ $employee->position ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $employee->department ?: '-' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($employee->status) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('employees.edit', $employee) }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('{{ __('Delete this employee?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-medium">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">{{ __('No employees found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $employees->withQueryString()->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const orgCard = document.querySelector('[data-manager-update-url-template]');
            const updateUrlTemplate = orgCard?.dataset.managerUpdateUrlTemplate || '/employees/__EMPLOYEE__/manager';
            const dropTargets = Array.from(document.querySelectorAll('[data-org-drop-target="1"]'));
            const draggableNodes = Array.from(document.querySelectorAll('.org-node[draggable="true"]'));
            let draggedEmployee = null;

            function clearHighlights() {
                dropTargets.forEach((target) => {
                    target.classList.remove('border-emerald-500', 'bg-emerald-50');
                });
            }

            draggableNodes.forEach((node) => {
                node.addEventListener('dragstart', function (event) {
                    draggedEmployee = {
                        id: node.dataset.employeeId,
                        name: node.dataset.employeeName || 'Employee',
                    };

                    node.classList.add('opacity-60');
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', draggedEmployee.id || '');
                });

                node.addEventListener('dragend', function () {
                    node.classList.remove('opacity-60');
                    clearHighlights();
                    draggedEmployee = null;
                });
            });

            dropTargets.forEach((target) => {
                target.addEventListener('dragover', function (event) {
                    if (!draggedEmployee) return;
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                    target.classList.add('border-emerald-500', 'bg-emerald-50');
                });

                target.addEventListener('dragleave', function () {
                    target.classList.remove('border-emerald-500', 'bg-emerald-50');
                });

                target.addEventListener('drop', async function (event) {
                    event.preventDefault();
                    clearHighlights();

                    if (!draggedEmployee || !draggedEmployee.id) return;

                    const managerId = target.dataset.managerId || null;
                    const managerName = target.dataset.managerName || 'No manager';

                    if (managerId && draggedEmployee.id === managerId) {
                        window.alert('An employee cannot report to themselves.');
                        return;
                    }

                    const confirmed = window.confirm(
                        'Confirm reassignment: ' + draggedEmployee.name + ' will report to ' + managerName + '.'
                    );

                    if (!confirmed) {
                        return;
                    }

                    try {
                        const updateUrl = updateUrlTemplate.replace('__EMPLOYEE__', draggedEmployee.id);
                        const response = await fetch(updateUrl, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken || '',
                            },
                            body: JSON.stringify({
                                manager_id: managerId,
                            }),
                        });

                        const payload = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            window.alert(payload.message || 'Unable to update manager assignment.');
                            return;
                        }

                        window.location.reload();
                    } catch (error) {
                        window.alert('Unexpected error while updating manager assignment.');
                    }
                });
            });
        });
    </script>
</x-app-layout>
