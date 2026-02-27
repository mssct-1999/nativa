<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    use BuildsMonthlyMetrics;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chart = $this->monthlyCountSeries(Employee::class);

        return view('employees.index', [
            'pageDescription' => 'Review team capacity, hiring trend, and compensation baseline.',
            'metrics' => [
                ['label' => 'Total employees', 'value' => number_format(Employee::query()->count())],
                ['label' => 'Active employees', 'value' => number_format(Employee::query()->where('status', 'active')->count())],
                ['label' => 'Avg. salary', 'value' => '$'.number_format((float) Employee::query()->avg('salary'), 2)],
                ['label' => 'New hires (30d)', 'value' => number_format(Employee::query()->where('created_at', '>=', now()->subDays(30))->count())],
                ['label' => 'Growth vs last month', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Employees added (last 6 months)',
                'labels' => $chart['labels'],
                'values' => $chart['values'],
            ],
        ]);
    }

    /**
     * Display the full list view used for CRUD operations.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return view('employees.list', [
            'employees' => Employee::query()->with(['user', 'employee'])->latest()->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employees.create', [
            'users' => User::query()->orderBy('name')->get(),
            'managers' => Employee::query()->orderBy('employee_number')->get(),
            'statuses' => ['active', 'inactive', 'terminated'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'employee_number' => ['nullable', 'string', 'max:255', 'unique:employees,employee_number'],
            'hire_date' => ['nullable', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'status' => ['required', Rule::in(['active', 'inactive', 'terminated'])],
        ]);

        Employee::create($validated);

        return redirect()->route('employees.list')->with('status', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', [
            'employee' => $employee,
            'users' => User::query()->orderBy('name')->get(),
            'managers' => Employee::query()->whereKeyNot($employee->id)->orderBy('employee_number')->get(),
            'statuses' => ['active', 'inactive', 'terminated'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'employee_number' => ['nullable', 'string', 'max:255', Rule::unique('employees', 'employee_number')->ignore($employee->id)],
            'hire_date' => ['nullable', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'manager_id' => ['nullable', 'exists:employees,id', Rule::notIn([$employee->id])],
            'status' => ['required', Rule::in(['active', 'inactive', 'terminated'])],
        ]);

        $employee->update($validated);

        return redirect()->route('employees.list')->with('status', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.list')->with('status', 'Employee deleted successfully.');
    }
}
