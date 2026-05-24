<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SettingsHelper;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffSalaryConfig;
use App\Models\StaffPayslip;
use App\Models\PayslipItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('hr_payroll_view')) abort(403);

        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $query = StaffPayslip::with(['staff', 'items']);

        // Filter by Month/Year
        $query->where('month', $month)->where('year', $year);

        // Filter by Staff
        $query->when($request->filled('staff_id'), function ($q) use ($request) {
            return $q->where('staffid', $request->staff_id);
        });

        // Filter by Status
        $query->when($request->filled('status'), function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        // Filter by Net Amount Range
        $query->when($request->filled('min_amount'), function ($q) use ($request) {
            return $q->where('net_amount', '>=', $request->min_amount);
        });
        $query->when($request->filled('max_amount'), function ($q) use ($request) {
            return $q->where('net_amount', '<=', $request->max_amount);
        });

        $perPage = SettingsHelper::get('tables_pagination_limit', 25);
        $payslips = $query->paginate($perPage)->withQueryString();

        return view('admin.payroll.index', compact('payslips', 'month', 'year'));
    }

    public function salaryConfig(Request $request, $id)
    {
        if (!Auth::user()->can('hr_payroll_manage')) abort(403);
        $staff = Staff::findOrFail($id);
        $config = StaffSalaryConfig::where('staffid', $id)->first() ?? new StaffSalaryConfig(['staffid' => $id]);
        return view('admin.payroll.salary_config', compact('staff', 'config'));
    }

    public function updateSalaryConfig(Request $request, $id)
    {
        if (!Auth::user()->can('hr_payroll_manage')) abort(403);

        $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'fixed_allowances' => 'nullable|numeric|min:0',
            'fixed_deductions' => 'nullable|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        StaffSalaryConfig::updateOrCreate(
            ['staffid' => $id],
            [
                'base_salary' => $request->base_salary,
                'fixed_allowances' => $request->fixed_allowances ?? 0,
                'fixed_deductions' => $request->fixed_deductions ?? 0,
                'effective_date' => $request->effective_date,
            ]
        );

        return redirect()->back()->with('success', 'Salary configuration updated successfully!');
    }

    public function generatePayslips(Request $request)
    {
        if (!Auth::user()->can('hr_payroll_manage')) abort(403);

        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
        ]);

        $month = $request->month;
        $year = $request->year;
        $staffMembers = Staff::where('active', 1)->get();
        $generatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($staffMembers as $staff) {
                $config = StaffSalaryConfig::where('staffid', $staff->staffid)->first();
                if (!$config) continue;

                $base = $config->base_salary;
                $additions = $config->fixed_allowances;
                $deductions = $config->fixed_deductions;

                // Basic net calculation
                $net = $base + $additions - $deductions;

                $defaultStatus = SettingsHelper::get('payroll_default_status', 'Draft');
                $payslip = StaffPayslip::create([
                    'staffid' => $staff->staffid,
                    'month' => $month,
                    'year' => $year,
                    'basic' => $base,
                    'total_additions' => $additions,
                    'total_deductions' => $deductions,
                    'net_amount' => $net,
                    'status' => $defaultStatus,
                ]);

                // Add base items
                PayslipItem::create(['payslip_id' => $payslip->id, 'type' => 'Addition', 'label' => 'Base Salary', 'amount' => $base]);
                if ($additions > 0) {
                    PayslipItem::create(['payslip_id' => $payslip->id, 'type' => 'Addition', 'label' => 'Fixed Allowances', 'amount' => $additions]);
                }
                if ($deductions > 0) {
                    PayslipItem::create(['payslip_id' => $payslip->id, 'type' => 'Deduction', 'label' => 'Fixed Deductions', 'amount' => $deductions]);
                }

                $generatedCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to generate payslips: ' . $e->getMessage());
        }

        return redirect()->route('admin.payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Generated $generatedCount payslips successfully!");
    }

    public function addAdjustment(Request $request)
    {
        if (!Auth::user()->can('hr_payroll_manage')) abort(403);

        $request->validate([
            'payslip_id' => 'required|exists:tblstaff_payslips,id',
            'type' => 'required|in:Addition,Deduction',
            'label' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $payslip = StaffPayslip::findOrFail($request->input('payslip_id'));
        
        PayslipItem::create([
            'payslip_id' => $payslip->id,
            'type' => $request->type,
            'label' => $request->label,
            'amount' => $request->amount,
        ]);

        // Recalculate Totals
        $totalAdditions = $payslip->items()->where('type', 'Addition')->sum('amount');
        $totalDeductions = $payslip->items()->where('type', 'Deduction')->sum('amount');
        $net = $payslip->basic + $totalAdditions - $totalDeductions;

        $payslip->update([
            'total_additions' => $totalAdditions,
            'total_deductions' => $totalDeductions,
            'net_amount' => $net,
        ]);

        return response()->json(['success' => true, 'net_amount' => $net]);
    }

    public function markAsPaid($id)
    {
        if (!Auth::user()->can('hr_payroll_manage')) abort(403);
        $payslip = StaffPayslip::findOrFail($id);
        $paidStatus = SettingsHelper::get('payroll_paid_status', 'Paid');
        $payslip->update([
            'status' => $paidStatus,
            'payment_date' => now(),
        ]);
        return redirect()->back()->with('success', 'Payslip marked as paid!');
    }

    public function myPayslips()
    {
        if (!Auth::user()->can('hr_payroll_view')) abort(403);
        $perPage = SettingsHelper::get('tables_pagination_limit', 25);
        $payslips = StaffPayslip::where('staffid', Auth::id())->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate($perPage);
        return view('admin.payroll.my_payslips', compact('payslips'));
    }

    public function viewPayslip($id)
    {
        $payslip = StaffPayslip::with(['staff', 'items'])->findOrFail($id);
        
        // Authorization: Only owner or has permission
        if ($payslip->staffid !== Auth::id() && !Auth::user()->can('hr_payroll_manage')) {
            abort(403);
        }

        return view('admin.payroll.view', compact('payslip'));
    }
}
