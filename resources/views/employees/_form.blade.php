@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="user_id" class="block text-sm font-medium text-slate-700">User</label>
        <select id="user_id" name="user_id" class="mt-1 w-full rounded-md border-slate-300">
            <option value="">None</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $employee->user_id ?? '') === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="employee_number" class="block text-sm font-medium text-slate-700">Employee number</label>
        <input id="employee_number" name="employee_number" type="text" value="{{ old('employee_number', $employee->employee_number ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('employee_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="position" class="block text-sm font-medium text-slate-700">Position</label>
        <input id="position" name="position" type="text" value="{{ old('position', $employee->position ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('position') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="department" class="block text-sm font-medium text-slate-700">Department</label>
        <input id="department" name="department" type="text" value="{{ old('department', $employee->department ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('department') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="hire_date" class="block text-sm font-medium text-slate-700">Hire date</label>
        <input id="hire_date" name="hire_date" type="date" value="{{ old('hire_date', isset($employee->hire_date) ? optional($employee->hire_date)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('hire_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="salary" class="block text-sm font-medium text-slate-700">Salary</label>
        <input id="salary" name="salary" type="number" min="0" step="0.01" value="{{ old('salary', $employee->salary ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('salary') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="manager_id" class="block text-sm font-medium text-slate-700">Manager</label>
        <select id="manager_id" name="manager_id" class="mt-1 w-full rounded-md border-slate-300">
            <option value="">None</option>
            @foreach ($managers as $manager)
                <option value="{{ $manager->id }}" @selected((string) old('manager_id', $employee->manager_id ?? '') === (string) $manager->id)>{{ $manager->employee_number ?: 'Employee #'.$manager->id }}</option>
            @endforeach
        </select>
        @error('manager_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
        <select id="status" name="status" required class="mt-1 w-full rounded-md border-slate-300">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $employee->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Save
    </button>
    <a href="{{ route('employees.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
</div>
