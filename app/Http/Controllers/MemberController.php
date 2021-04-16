<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;
use App\Models\{
    TeamMember,
    User
};
use Hash;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->type != 'team-admin') {
            return Redirect::to('admin');
        }
        $team = $user->team();
        return view('admin.member', ['members' => $team->members]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //TODO validations
        $user = User::where('email', $request->email)->first();
        if ($user){
            return response()->json(['success' => false], 200);
        }
        $user = User::create([
            'email' => $request->email,
            'type'  => 'team-member',
            'name'  => $request->name,
            'password' =>  Hash::make($request->password)
        ]);

        $team = Auth::user()->team();
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id
        ]);

        return response()->json(['success' => true], 200);
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
        //TODO validations
        \Log::debug([$request->all(), $id]);
        $team_member = TeamMember::find($id);
        $team_member->user->name = $request->name;
        $team_member->user->email = $request->email;
        $team_member->push();
        return response()->json(['success' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $team_member = TeamMember::find($id);
        $team_member->user->inactive = 1;
        $team_member->push();
        $team_member->delete();
        return response()->json(['success' => true], 200);
    }
}
