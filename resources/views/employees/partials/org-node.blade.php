@php
    /** @var array{employee: \App\Models\Employee, children: \Illuminate\Support\Collection} $node */
    $employee = $node['employee'];
    $children = $node['children'];
    $displayName = optional($employee->user)->name ?? ($employee->employee_number ?: 'Employee #'.$employee->id);
@endphp

<li class="relative pl-6">
    <span class="absolute left-0 top-4 h-px w-4 bg-slate-300"></span>
    <div
        draggable="true"
        class="org-node rounded-lg border border-slate-200 bg-white px-3 py-2 transition hover:border-emerald-300 hover:shadow-sm cursor-grab active:cursor-grabbing"
        data-employee-id="{{ $employee->id }}"
        data-employee-name="{{ $displayName }}"
        data-org-drop-target="1"
        data-manager-id="{{ $employee->id }}"
        data-manager-name="{{ $displayName }}"
    >
        <p class="text-sm font-semibold text-slate-900">
            {{ $displayName }}
        </p>
        <p class="text-xs text-slate-600">
            {{ $employee->position ?: 'No position' }}
            <span class="text-slate-400">|</span>
            {{ $employee->employee_number ?: 'No employee #' }}
        </p>
    </div>

    @if ($children->isNotEmpty())
        <ul class="relative mt-2 space-y-2 border-l border-slate-300 pl-4">
            @foreach ($children as $childNode)
                @include('employees.partials.org-node', ['node' => $childNode])
            @endforeach
        </ul>
    @endif
</li>
