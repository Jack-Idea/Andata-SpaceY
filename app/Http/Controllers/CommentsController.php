<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   

        return view('layouts.comments');
    }

    public function apiShow() {
        
        return [
            'data' => Comment::all()->sortBy('id')->reverse()->values()
        ];
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

        $this->validate($request, [
            'username' => 'required',
            'email' => 'required|email',
            'title' => 'required'
        ]);

        Comment::create([
            'username' => request('username'),
            'email' => request('email'),
            'title' => request('title'),
            'descriptions' => request('description')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    
    public function editComment(Request $request, $id) {
        
        $contact = Comment::find($id);

        $contact->title = request('title');
        $contact->descriptions = request('description');

        $contact->save();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Comment::destroy($id);

        return response()->json(null, 204);
    }
}
