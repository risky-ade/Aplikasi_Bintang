<?php

namespace App\Http\Controllers;

use App\Models\OperationalExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationalExpenseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'kategori' => 'nullable|string|max:100',
        ]);

        $query = OperationalExpense::query()->with('user');

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('tanggal', [$request->from, $request->to]);
        } elseif ($request->filled('from')) {
            $query->whereDate('tanggal', '>=', $request->from);
        } elseif ($request->filled('to')) {
            $query->whereDate('tanggal', '<=', $request->to);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', 'like', '%' . $request->kategori . '%');
        }

        $expenses = $query->latest('tanggal')->latest('id')->get();
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

        OperationalExpense::create($data);

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

        $operationalExpense->update($data);

        return redirect()->route('operational_expenses.index')
            ->with('success', 'Biaya operasional berhasil diperbarui.');
    }

    public function destroy(OperationalExpense $operationalExpense)
    {
        $operationalExpense->delete();

        return redirect()->route('operational_expenses.index')
            ->with('success', 'Biaya operasional berhasil dihapus.');
    }
}
