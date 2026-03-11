<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMonthlyMetrics;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    use BuildsMonthlyMetrics;

    /**
     * Display payroll overview metrics.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chart = $this->monthlyCountSeries(Payroll::class);

        return view('payrolls.index', [
            'pageDescription' => 'Track payroll totals, processing status, and payout progress.',
            'metrics' => [
                ['label' => 'Total payrolls', 'value' => number_format(Payroll::query()->count())],
                ['label' => 'Pending payrolls', 'value' => number_format(Payroll::query()->where('status', 'pending')->count())],
                ['label' => 'Paid payrolls', 'value' => number_format(Payroll::query()->where('status', 'paid')->count())],
                ['label' => 'Net payouts', 'value' => '$'.number_format((float) Payroll::query()->sum('net'), 2)],
                ['label' => 'Growth vs last month', 'value' => $this->monthlyTrend($chart['values'])],
            ],
            'chart' => [
                'label' => 'Payrolls created (last 6 months)',
                'labels' => $chart['labels'],
                'values' => $chart['values'],
            ],
        ]);
    }

    /**
     * Display employees with their related payrolls.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = Employee::query()
            ->with(['user', 'payrolls' => function ($query) {
                $query->latest('period_end');
            }])
            ->orderBy('employee_number')
            ->orderBy('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('employee_number', 'like', '%'.$search.'%')
                    ->orWhere('position', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        return view('payrolls.list', [
            'employees' => $query->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new payroll.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('payrolls.create', [
            'employees' => Employee::query()->with('user')->orderBy('employee_number')->orderBy('id')->get(),
            'statuses' => ['pending', 'paid', 'failed'],
        ]);
    }

    /**
     * Store a newly created payroll.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $this->validatePayroll($request);

        Payroll::create($validated);

        return redirect()->route('payrolls.list')->with('status', 'Payroll created successfully.');
    }

    /**
     * Display the specified payroll.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function show(Payroll $payroll)
    {
        return redirect()->route('payrolls.edit', $payroll);
    }

    /**
     * Show the form for editing payroll.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function edit(Payroll $payroll)
    {
        return view('payrolls.edit', [
            'payroll' => $payroll,
            'employees' => Employee::query()->with('user')->orderBy('employee_number')->orderBy('id')->get(),
            'statuses' => ['pending', 'paid', 'failed'],
        ]);
    }

    /**
     * Update the specified payroll.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payroll $payroll)
    {
        $validated = $this->validatePayroll($request);

        $payroll->update($validated);

        return redirect()->route('payrolls.list')->with('status', 'Payroll updated successfully.');
    }

    /**
     * Remove the specified payroll.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payrolls.list')->with('status', 'Payroll deleted successfully.');
    }

    /**
     * Export saved payrolls to PDF.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportPdf()
    {
        $payrolls = Payroll::query()
            ->with(['employee.user'])
            ->orderByDesc('period_end')
            ->orderByDesc('id')
            ->get();

        $lines = [
            'NATIVA PAYROLL EXPORT',
            'Generated at: '.now()->format('Y-m-d H:i'),
            'Total payrolls: '.$payrolls->count(),
            '',
        ];

        if ($payrolls->isEmpty()) {
            $lines[] = 'No payroll records found.';
        } else {
            foreach ($payrolls as $payroll) {
                $employeeName = optional(optional($payroll->employee)->user)->name
                    ?? (optional($payroll->employee)->employee_number ?: 'Employee #'.$payroll->employee_id);

                $lines[] = sprintf(
                    '#%d | %s | %s to %s | Gross: $%s | Taxes: $%s | Net: $%s | %s',
                    $payroll->id,
                    $employeeName,
                    optional($payroll->period_start)->format('Y-m-d'),
                    optional($payroll->period_end)->format('Y-m-d'),
                    number_format((float) $payroll->gross, 2),
                    number_format((float) $payroll->taxes, 2),
                    number_format((float) $payroll->net, 2),
                    strtoupper($payroll->status)
                );
            }
        }

        $pdfBinary = $this->buildSimplePdf($lines);
        $filename = 'payrolls-'.now()->format('Ymd-His').'.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Validate request data and normalize payroll fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    protected function validatePayroll(Request $request): array
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'gross' => ['required', 'numeric', 'min:0'],
            'taxes' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['pending', 'paid', 'failed'])],
            'paid_at' => ['nullable', 'date'],
        ]);

        $gross = (float) $validated['gross'];
        $taxes = (float) $validated['taxes'];
        $validated['net'] = max(0, $gross - $taxes);

        if ($validated['status'] === 'paid' && empty($validated['paid_at'])) {
            $validated['paid_at'] = now()->toDateString();
        }

        if ($validated['status'] !== 'paid') {
            $validated['paid_at'] = null;
        }

        return $validated;
    }

    /**
     * Build a minimal text-only PDF binary without external packages.
     *
     * @param  array<int, string>  $lines
     * @return string
     */
    protected function buildSimplePdf(array $lines): string
    {
        $pages = [];
        $chunkedLines = array_chunk($lines, 42);

        foreach ($chunkedLines as $chunk) {
            $stream = "BT\n/F1 10 Tf\n50 760 Td\n14 TL\n";

            foreach ($chunk as $line) {
                $escaped = $this->escapePdfText($line);
                $stream .= sprintf("(%s) Tj\nT*\n", $escaped);
            }

            $stream .= "ET";
            $pages[] = $stream;
        }

        if (empty($pages)) {
            $pages[] = "BT\n/F1 10 Tf\n50 760 Td\n14 TL\n(No data) Tj\nET";
        }

        $objects = [];
        $objectNumber = 1;
        $addObject = function (string $body) use (&$objects, &$objectNumber): int {
            $id = $objectNumber;
            $objects[$id] = $body;
            $objectNumber++;

            return $id;
        };

        $catalogObjectId = $addObject('<< /Type /Catalog /Pages 2 0 R >>');
        $pagesRootObjectId = $addObject('<< /Type /Pages /Kids [] /Count 0 >>');
        $fontObjectId = $addObject('<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>');

        $pageObjectIds = [];

        foreach ($pages as $pageStream) {
            $contentObjectId = $addObject(sprintf(
                "<< /Length %d >>\nstream\n%s\nendstream",
                strlen($pageStream),
                $pageStream
            ));

            $pageObjectIds[] = $addObject(sprintf(
                '<< /Type /Page /Parent %d 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 %d 0 R >> >> /Contents %d 0 R >>',
                $pagesRootObjectId,
                $fontObjectId,
                $contentObjectId
            ));
        }

        $kids = implode(' ', array_map(fn ($id) => $id.' 0 R', $pageObjectIds));
        $objects[$pagesRootObjectId] = sprintf(
            '<< /Type /Pages /Kids [%s] /Count %d >>',
            $kids,
            count($pageObjectIds)
        );

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $objectNumber => $body) {
            $offsets[$objectNumber] = strlen($pdf);
            $pdf .= $objectNumber." 0 obj\n".$body."\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer\n";
        $pdf .= "<< /Size ".(count($objects) + 1)." /Root ".$catalogObjectId." 0 R >>\n";
        $pdf .= "startxref\n".$xrefPosition."\n%%EOF";

        return $pdf;
    }

    protected function escapePdfText(string $text): string
    {
        $safe = str_replace('\\', '\\\\', $text);
        $safe = str_replace('(', '\\(', $safe);
        $safe = str_replace(')', '\\)', $safe);

        return preg_replace('/[^\x20-\x7E]/', '', $safe) ?: '';
    }
}
