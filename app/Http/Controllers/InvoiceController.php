<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    use BuildsMonthlyMetrics;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chart = $this->monthlyCountSeries(Invoice::class);

        return view('invoices.index', [
            'pageDescription' => 'Track billing throughput and payment collection health.',
            'metrics' => [
                ['label' => 'Total invoices', 'value' => number_format(Invoice::query()->count())],
                ['label' => 'Paid invoices', 'value' => number_format(Invoice::query()->where('status', 'paid')->count())],
                ['label' => 'Outstanding amount', 'value' => '$'.number_format((float) Invoice::query()->where('status', '!=', 'paid')->sum('total'), 2)],
                ['label' => 'Issued in 30 days', 'value' => number_format(Invoice::query()->where('created_at', '>=', now()->subDays(30))->count())],
                ['label' => 'Growth vs last month', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Invoices created (last 6 months)',
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
        return view('invoices.list', [
            'invoices' => Invoice::query()->with(['client', 'user'])->latest()->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('invoices.create', [
            'clients' => Client::query()->orderBy('company_name')->get(),
            'quotes' => Quote::query()->orderBy('number')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'statuses' => ['draft', 'issued', 'paid', 'overdue', 'cancelled'],
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
            'number' => ['required', 'string', 'max:255', 'unique:invoices,number'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(['draft', 'issued', 'paid', 'overdue', 'cancelled'])],
            'sub_total' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Invoice::create($validated);

        return redirect()->route('invoices.list')->with('status', 'Invoice created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', [
            'invoice' => $invoice,
            'clients' => Client::query()->orderBy('company_name')->get(),
            'quotes' => Quote::query()->orderBy('number')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'statuses' => ['draft', 'issued', 'paid', 'overdue', 'cancelled'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'number' => ['required', 'string', 'max:255', Rule::unique('invoices', 'number')->ignore($invoice->id)],
            'client_id' => ['nullable', 'exists:clients,id'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(['draft', 'issued', 'paid', 'overdue', 'cancelled'])],
            'sub_total' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'issued_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.list')->with('status', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.list')->with('status', 'Invoice deleted successfully.');
    }
}
