<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
    public function list(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $organizationEmployees = Employee::query()
            ->with('user')
            ->orderBy('employee_number')
            ->orderBy('id')
            ->get();

        $query = Employee::query()->with(['user', 'employee']);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('employee_number', 'like', '%'.$search.'%')
                    ->orWhere('position', 'like', '%'.$search.'%')
                    ->orWhere('department', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        return view('employees.list', [
            'employees' => $query->latest()->paginate(15)->withQueryString(),
            'organizationTree' => $this->buildOrganizationTree($organizationEmployees),
            'search' => $search,
        ]);
    }

    /**
     * Build a manager-based organization tree for display in the list view.
     */
    protected function buildOrganizationTree(Collection $employees): Collection
    {
        $employeesById = $employees->keyBy('id');
        $childrenByManager = $employees->groupBy('manager_id');

        $roots = $employees->filter(function (Employee $employee) use ($employeesById) {
            if ($employee->manager_id === null || $employee->manager_id === $employee->id) {
                return true;
            }

            return ! $employeesById->has($employee->manager_id);
        })->values();

        $visited = [];
        $tree = $roots->map(function (Employee $employee) use (&$visited, $childrenByManager) {
            return $this->buildTreeNode($employee, $childrenByManager, $visited, []);
        })->values();

        // Include any employees skipped due to cyclic or disconnected manager links.
        $remaining = $employees->reject(fn (Employee $employee) => isset($visited[$employee->id]))->values();

        $extraRoots = $remaining->map(function (Employee $employee) use (&$visited, $childrenByManager) {
            return $this->buildTreeNode($employee, $childrenByManager, $visited, []);
        })->values();

        return $tree->concat($extraRoots)->values();
    }

    /**
     * Recursively build one node and its descendants, guarding against cycles.
     *
     * @param  array<int, bool>  $visited
     * @param  array<int, bool>  $path
     * @return array{employee: Employee, children: Collection<int, array>}
     */
    protected function buildTreeNode(Employee $employee, Collection $childrenByManager, array &$visited, array $path): array
    {
        if (isset($path[$employee->id])) {
            return [
                'employee' => $employee,
                'children' => collect(),
            ];
        }

        $visited[$employee->id] = true;
        $path[$employee->id] = true;

        $children = $childrenByManager->get($employee->id, collect())
            ->filter(fn (Employee $child) => ! isset($path[$child->id]))
            ->sortBy(fn (Employee $child) => [$child->employee_number ?? '', $child->id])
            ->values()
            ->map(function (Employee $child) use ($childrenByManager, &$visited, $path) {
                return $this->buildTreeNode($child, $childrenByManager, $visited, $path);
            })->values();

        return [
            'employee' => $employee,
            'children' => $children,
        ];
    }

    /**
     * Reassign an employee under a manager from the organization tree drag-and-drop UI.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateManager(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'manager_id' => ['nullable', 'integer', 'exists:employees,id', Rule::notIn([$employee->id])],
        ]);

        $managerId = $validated['manager_id'] ?? null;

        if ($managerId !== null && $this->createsManagerCycle($employee->id, $managerId)) {
            return response()->json([
                'message' => 'Invalid manager assignment. This change would create a reporting cycle.',
            ], 422);
        }

        $employee->update(['manager_id' => $managerId]);

        return response()->json([
            'message' => 'Manager assignment updated successfully.',
        ]);
    }

    /**
     * Check whether assigning $proposedManagerId to $employeeId would create a cycle.
     */
    protected function createsManagerCycle(int $employeeId, int $proposedManagerId): bool
    {
        $visited = [];
        $current = Employee::query()->select(['id', 'manager_id'])->find($proposedManagerId);

        while ($current !== null) {
            if (isset($visited[$current->id])) {
                return true;
            }

            if ((int) $current->id === $employeeId) {
                return true;
            }

            $visited[$current->id] = true;

            if ($current->manager_id === null) {
                return false;
            }

            $current = Employee::query()->select(['id', 'manager_id'])->find($current->manager_id);
        }

        return false;
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
