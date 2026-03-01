<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Payrolls List') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('payrolls.export.pdf') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Export PDF
                </a>
                <a href="{{ route('payrolls.create') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                    Add new payroll
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Employee</th>
                        <th class="px-4 py-3 text-left">Payrolls</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $employee)
                        @php
                            $employeeName = optional($employee->user)->name ?? ($employee->employee_number ?: 'Employee #'.$employee->id);
                        @endphp
                        <tr>
                            <td class="px-4 py-4 align-top">
                                <p class="font-semibold text-slate-900">{{ $employeeName }}</p>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $employee->employee_number ?: 'No employee #' }}
                                    <span class="mx-1">|</span>
                                    {{ $employee->position ?: 'No position' }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                @if ($employee->payrolls->isEmpty())
                                    <p class="text-sm text-slate-500">No payroll records for this employee.</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach ($employee->payrolls as $payroll)
                                            <div class="rounded-lg border border-slate-200 p-3">
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <div class="text-sm text-slate-700">
                                                        <span class="font-medium">{{ optional($payroll->period_start)->format('Y-m-d') }}</span>
                                                        to
                                                        <span class="font-medium">{{ optional($payroll->period_end)->format('Y-m-d') }}</span>
                                                        <span class="mx-2 text-slate-300">|</span>
                                                        Net: <span class="font-semibold">${{ number_format((float) $payroll->net, 2) }}</span>
                                                        <span class="mx-2 text-slate-300">|</span>
                                                        Status: <span class="font-medium">{{ ucfirst($payroll->status) }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <a href="{{ route('payrolls.edit', $payroll) }}" class="text-amber-600 hover:text-amber-700 font-medium">Edit</a>
                                                        <form method="POST" action="{{ route('payrolls.destroy', $payroll) }}" onsubmit="return confirm('Delete this payroll?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-6 text-center text-slate-500">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $employees->links() }}
        </div>
    </div>
</x-app-layout>
