<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LoanRepayment;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '12000');
        ini_set('request_terminate_time', '12000');
    }

    public function admin_recon(Request $request)
    {

        if ($request->branch == null) {
            $data = DB::select("select 
            name,
            IFNULL((select sum(debit) from payments where status = 'pending' and transaction_type='savings' and created_by=u.name),0) as savings,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and created_by=u.name),0) as withdrawals,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as unconfirmed_pof,
            IFNULL((select sum(credit) from payments where status = 'confirmed' and reconciled='0' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as pof,
            IFNULL((select sum(amount) from loan_repayments where status = 'pending' and handler=u.name), 0) as loan_collection
            from users u where sales_executive='1' and branch='Asaba' order by savings desc;");

            $total_expected = 0;
            foreach ($data as $item) {
                $total_expected = $total_expected + $item->savings + $item->loan_collection - $item->pof;
            }
        } else {
            $data = DB::select("select 
            name,
            IFNULL((select sum(debit) from payments where status = 'pending' and transaction_type='savings' and created_by=u.name),0) as savings,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and created_by=u.name),0) as withdrawals,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as unconfirmed_pof,
            IFNULL((select sum(credit) from payments where status = 'confirmed' and reconciled='0' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as pof,
            IFNULL((select sum(amount) from loan_repayments where status = 'pending' and handler=u.name), 0) as loan_collection
            from users u where sales_executive='1' and branch='" . $request->branch . "' order by savings desc;");

            $total_expected = 0;
            foreach ($data as $item) {
                $total_expected = $total_expected + $item->savings + $item->loan_collection - $item->pof;
            }
        }
        $branches = Branch::all();
        return view('ops.admin_recon')->with(['data' => $data, 'total_expected' => $total_expected, 'branches' => $branches]);
    }

    public function recon_by_date(Request $request)
    {
        $branches = Branch::all();
        $branch = auth()->user()->branch;

        if ($request->branch == null) {
            $recons = Payments::where('branch', 'Asaba')->where('status', 'confirmed')->where('remarks', '!=', 'Opening Balance')->latest()->get()->groupBy(function ($item) {
                return $item->created_at->format('d-M-y');
            });
            $result = array();
            foreach ($recons as $item) {
                $deposits = 0;
                $withdrawals = 0;
                $charges = 0;
                $regfees = 0;
                $data = array();
                $data['date'] = $item[0]->created_at->format('d-m-Y');;
                foreach ($item as $it) {
                    if ($it->transaction_type == 'savings') {
                        $deposits = $deposits + $it->debit;
                    }
                    if ($it->transaction_type == 'withdrawal') {
                        $withdrawals = $withdrawals + $it->credit;
                    }
                    if ($it->transaction_type == 'charge') {
                        $charges = $charges + $it->credit;
                    }
                    if ($it->transaction_type == 'registration') {
                        $regfees = $regfees + $it->debit;
                    }
                }
                $data['deposits'] = $deposits;
                $data['withdrawals'] = $withdrawals;
                $data['charges'] = $charges;
                $data['regfees'] = $regfees;
                $result[] = $data;
            }

            //get loans 
            $loans = LoanRepayment::where('branch', auth()->user()->branch)->where('status', 'confirmed')->latest()->get()->groupBy(function ($item) {
                return $item->created_at->format('d-M-y');
            });
            return view('ops.reconc_by_date')->with(['data' => $result, 'branches' => $branches]);
        } else {
            $recons = Payments::where('branch', auth()->user()->branch)->where('status', 'confirmed')->where('remarks', '!=', 'Opening Balance')->latest()->get()->groupBy(function ($item) {
                return $item->created_at->format('d-M-y');
            });
            $result = array();
            foreach ($recons as $item) {
                $deposits = 0;
                $withdrawals = 0;
                $charges = 0;
                $regfees = 0;
                $data = array();
                $data['date'] = $item[0]->created_at->format('d-m-Y');;
                foreach ($item as $it) {
                    if ($it->transaction_type == 'savings') {
                        $deposits = $deposits + $it->debit;
                    }
                    if ($it->transaction_type == 'withdrawal') {
                        $withdrawals = $withdrawals + $it->credit;
                    }
                    if ($it->transaction_type == 'charge') {
                        $charges = $charges + $it->credit;
                    }
                    if ($it->transaction_type == 'registration') {
                        $regfees = $regfees + $it->debit;
                    }
                }
                $data['deposits'] = $deposits;
                $data['withdrawals'] = $withdrawals;
                $data['charges'] = $charges;
                $data['regfees'] = $regfees;
                $result[] = $data;
            }

            //get loans 
            $loans = LoanRepayment::where('branch', auth()->user()->branch)->where('status', 'confirmed')->latest()->get()->groupBy(function ($item) {
                return $item->created_at->format('d-M-y');
            });
            return view('ops.reconc_by_date')->with(['data' => $result, 'branches' => $branches]);
        }
    }
}
