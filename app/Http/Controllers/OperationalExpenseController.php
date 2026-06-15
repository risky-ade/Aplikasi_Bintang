<?php

namespace App\Http\Controllers;

use App\Models\OperationalExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class OperationalExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->validateFilter($request);

        $expenses = $this->filteredQuery($request)->latest('tanggal')->latest('id')->get();
        $total = $expenses->sum('nominal');

        return view('operational_expenses.index', compact('expenses', 'total'));
    }

    public function create()
    {
        return view('operational_expenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ]);

        $data['created_by'] = Auth::id();

        $expense = OperationalExpense::create($data);

        Log::channel('biaya_operasional')->info('Biaya operasional berhasil disimpan', [
            'expense_id' => $expense->id,
            'tanggal' => $expense->tanggal?->format('Y-m-d'),
            'kategori' => $expense->kategori,
            'nominal' => $expense->nominal,
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return redirect()->route('operational_expenses.index')
            ->with('success', 'Biaya operasional berhasil disimpan.');
    }

    public function edit(OperationalExpense $operationalExpense)
    {
        return view('operational_expenses.edit', compact('operationalExpense'));
    }

    public function update(Request $request, OperationalExpense $operationalExpense)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ]);

        $before = $operationalExpense->only(['tanggal', 'kategori', 'keterangan', 'nominal']);
        $operationalExpense->update($data);

        Log::channel('biaya_operasional')->info('Biaya operasional berhasil diperbarui', [
            'expense_id' => $operationalExpense->id,
            'before' => $before,
            'after' => $operationalExpense->only(['tanggal', 'kategori', 'keterangan', 'nominal']),
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return redirect()->route('operational_expenses.index')
            ->with('success', 'Biaya operasional berhasil diperbarui.');
    }

    public function destroy(OperationalExpense $operationalExpense)
    {
        $expenseData = $operationalExpense->only(['id', 'tanggal', 'kategori', 'keterangan', 'nominal']);
        $operationalExpense->delete();

        Log::channel('biaya_operasional')->warning('Biaya operasional dihapus', [
            'expense' => $expenseData,
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return redirect()->route('operational_expenses.index')
            ->with('success', 'Biaya operasional berhasil dihapus.');
    }

    public function pdf(Request $request)
    {
        $this->validateFilter($request);

        $expenses = $this->filteredQuery($request)->latest('tanggal')->latest('id')->get();
        $total = $expenses->sum('nominal');

        Log::channel('biaya_operasional')->info('Export PDF biaya operasional', [
            'filter' => $request->only(['from', 'to', 'kategori']),
            'total_data' => $expenses->count(),
            'total_nominal' => $total,
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        $pdf = Pdf::loadView('operational_expenses.pdf', compact('expenses', 'total'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('biaya-operasional.pdf');
    }

    private function validateFilter(Request $request): void
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'kategori' => 'nullable|string|max:100',
        ]);
    }

    private function filteredQuery(Request $request)
    {
        return OperationalExpense::query()
            ->with('user')
            ->when($request->filled('from') && $request->filled('to'), function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->from, $request->to]);
            })
            ->when($request->filled('from') && ! $request->filled('to'), function ($q) use ($request) {
                $q->whereDate('tanggal', '>=', $request->from);
            })
            ->when(! $request->filled('from') && $request->filled('to'), function ($q) use ($request) {
                $q->whereDate('tanggal', '<=', $request->to);
            })
            ->when($request->filled('kategori'), function ($q) use ($request) {
                $q->where('kategori', 'like', '%' . $request->kategori . '%');
            });
    }
}
