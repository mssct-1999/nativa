@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="employee_id" class="block text-sm font-medium text-slate-700">Employee</label>
        <select id="employee_id" name="employee_id" required class="mt-1 w-full rounded-md border-slate-300">
            <option value="">Select employee</option>
            @foreach ($employees as $employeeOption)
                @php
                    $name = optional($employeeOption->user)->name ?? ($employeeOption->employee_number ?: 'Employee #'.$employeeOption->id);
                @endphp
                <option value="{{ $employeeOption->id }}" @selected((string) old('employee_id', $payroll->employee_id ?? '') === (string) $employeeOption->id)>
                    {{ $name }} ({{ $employeeOption->employee_number ?: 'No #' }})
                </option>
            @endforeach
        </select>
        @error('employee_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
        <select id="status" name="status" required class="mt-1 w-full rounded-md border-slate-300">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $payroll->status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="period_start" class="block text-sm font-medium text-slate-700">Period start</label>
        <input id="period_start" name="period_start" type="date" required value="{{ old('period_start', isset($payroll->period_start) ? optional($payroll->period_start)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('period_start') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="period_end" class="block text-sm font-medium text-slate-700">Period end</label>
        <input id="period_end" name="period_end" type="date" required value="{{ old('period_end', isset($payroll->period_end) ? optional($payroll->period_end)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('period_end') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="gross" class="block text-sm font-medium text-slate-700">Gross</label>
        <input id="gross" name="gross" type="number" min="0" step="0.01" required value="{{ old('gross', $payroll->gross ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('gross') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="taxes" class="block text-sm font-medium text-slate-700">Taxes</label>
        <input id="taxes" name="taxes" type="number" min="0" step="0.01" required value="{{ old('taxes', $payroll->taxes ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('taxes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="paid_at" class="block text-sm font-medium text-slate-700">Paid at</label>
        <input id="paid_at" name="paid_at" type="date" value="{{ old('paid_at', isset($payroll->paid_at) ? optional($payroll->paid_at)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('paid_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="rounded-lg bg-slate-50 p-3">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Net</p>
        <p class="mt-1 text-base font-semibold text-slate-900">Calculated automatically as Gross - Taxes</p>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Save
    </button>
    <a href="{{ route('payrolls.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
</div>
