<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TargetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (auth()->user()->operations_manager == true) {
            $targets = Target::all();
            $branches = Branch::all();
            $data = array();

            foreach ($branches as $item) {
                $target = Target::where('branch', $item->name)
                    ->where('month', intval(date('m')))
                    ->where('year', intval(date('Y')))
                    ->first();

                if ($target == null) {
                    $data[] = [
                        'branch' => $item->name,
                        'target' => 0
                    ];
                } else {
                    $data[] = [
                        'branch' => $item->name,
                        'target' => $target->amount
                    ];
                }
            }

            //get Target per branch
            return view('operations.targets.index')->with(['data' => $data]);
        } else {
            return abort(401);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $target = Target::where('branch', $id)
            ->where('month', intval(date('m')) - 1)
            ->where('year', intval(date('Y')) - 1)
            ->first();

        $prev_target = 0;

        if ($target != null) {
            $prev_target = $target->amount;
        }

        return view('operations.targets.create')->with(['prev_target'=>$prev_target, 'branch'=>$id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $branch = $request->branch;
        $amount = $request->amount;

        $target = Target::create([
            'amount'=>$amount,
            'branch'=>$branch,
            'month'=>intval(date('m')),
            'year'=>intval(date('Y'))
        ]);
        
        return redirect()->route('admin.targets.list');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
