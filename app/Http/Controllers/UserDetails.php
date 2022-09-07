<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserDetails as ModelsUserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserDetails extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->admin==true) {
            $users = User::paginate(10);
            return view('admin.fieldteam.index')->with(['data' => $users]);
        } else {
            abort(401);
        }
    }

    public function create()
    {
        if (auth()->user()->role) {
            $roles = Role::all();
            $branch = Branch::all();
            return view('admin.fieldteam.create')->with(['roles' => $roles, 'branches' => $branch]);
        } else {
            abort(401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->role) {
            $validated = $request->validate([
                'username' => 'required|unique:users,email|max:255',
                'name' => 'required',
                'phone' => 'required|numeric|min:10',
                'role' => 'required',
                'branch' => 'required',
                'active' => 'required',
            ]);

            if ($validated['role'] == "Sales Executive") {
                $sec3 = true;
                $od = false;
                $manager = false;
                $assm = false;
                $adm = false;
            } else if ($validated['role'] == "Office Administrator") {
                $sec3 = false;
                $od = true;
                $manager = false;
                $assm = false;
                $adm = false;
            } else if ($validated['role'] == "Branch Manager") {
                $sec3 = false;
                $od = false;
                $manager = true;
                $assm = false;
                $adm = false;
            } else if ($validated['role'] == 'Assistant Branch Manager') {
                $sec3 = false;
                $od = false;
                $manager = false;
                $assm = true;
                $adm = false;
            } else if ($validated['role'] == 'Admin') {
                $sec3 = false;
                $od = false;
                $manager = false;
                $assm = false;
                $adm = true;
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['username'],
                'phone' => $validated['phone'],
                'branch' => $validated['branch'],
                'sales executive' => $sec3,
                'admin' => $adm,
                'assistant manager' => $assm,
                'office admin' => $od,
                'branch manager' => $manager,
                'password' => Hash::make('password')
            ]);

            return redirect()->route('admin.team.list');
        } else {
            abort(401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (auth()->user()->role) {
            $user = User::where('id', 1)->get()->first();
            return view('admin.fieldteam.edit')->with(['user' => $user]);
        } else {
            abort(401);
        }
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
        if (auth()->user()->role) {
            $validated = $request->validate([
                'username' => 'required|unique:users,email|max:255',
                'name' => 'required',
                'phone' => 'required|numeric|min:10',
                'role' => 'required',
                'branch' => 'required',
                'active' => 'required',
            ]);

            if ($validated['role'] == "Sales Executive") {
                $sec3 = true;
                $od = false;
                $manager = false;
                $assm = false;
                $adm = false;
            } else if ($validated['role'] == "Office Administrator") {
                $sec3 = false;
                $od = true;
                $manager = false;
                $assm = false;
                $adm = false;
            } else if ($validated['role'] == "Branch Manager") {
                $sec3 = false;
                $od = false;
                $manager = true;
                $assm = false;
                $adm = false;
            } else if ($validated['role'] == 'Assistant Branch Manager') {
                $sec3 = false;
                $od = false;
                $manager = false;
                $assm = true;
                $adm = false;
            } else if ($validated['role'] == 'Admin') {
                $sec3 = false;
                $od = false;
                $manager = false;
                $assm = false;
                $adm = true;
            }

            $user = User::where('id', $$request->id)->update([
                'name' => $validated['name'],
                'email' => $validated['username'],
                'phone' => $validated['phone'],
                'branch' => $validated['branch'],
                'sales executive' => $sec3,
                'admin' => $adm,
                'assistant manager' => $assm,
                'office admin' => $od,
                'branch manager' => $manager,
                'password' => Hash::make('password')
            ]);

            return redirect()->route('admin.team.list');
        } else {
            abort(401);
        }
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
