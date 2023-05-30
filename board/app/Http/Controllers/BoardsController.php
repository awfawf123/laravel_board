<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Boards;

class BoardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = Boards::select(['id','title','hits','created_at','updated_at'])->orderBy('hits','desc')->get();
        return view('list')->with('data',$result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('write');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $req
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $req->validate([
            'title' => 'required |between:3,30'
            ,'content' => 'required |max:1000'
        ]);

        $boards = new Boards([
            'title'=> $req->input('title')
            ,'content' => $req->input('content')
        ]);
        $boards->save();
        return redirect('/boards');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $boards = Boards::find($id); // 해당하는 레코드를 찾아서 넘겨줌 실패시 false넘겨줌
        $boards->hits++;
        $boards->save();
        return view('detail')->with('data', Boards::findOrFail($id)); //findOrFail 실패시 404
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $boards = Boards::find($id);
        return view('edit')->with('data',$boards);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $req
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $id)
    {   
        // DB::table('Boards')->where('id','=',$id)->update([
        //     'title'=> $req->title
        //     ,'content' => $req->content
        // ]);
        // 1
        $arr = ['id' => $id];
        $req->request->add($arr);

        $req->validate([
            'title' => 'required |between:3,30'
            ,'content' => 'required |max:1000'
            ,'id' => 'required | integer'
        ]);

        $result = Boards::find($id);
        $result->title = $req->title;
        $result->content = $req->content;
        $result->save();
        
        return redirect('/boards/'.$id);

        // 2
        $validator = Validator::make(
            $request->only('id','title','content')
            ,[
                'title' => 'required |between:3,30'
                ,'content' => 'required |max:1000'
                ,'id' => 'required | integer'
            ]
            );
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput($request->only('title','content'));
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
        // DB::delete() : 해당 레코드가 삭제 됨
        // Boards::destroy($id);
        Boards::find($id)->delete();
        return redirect('/boards'); 
    }

}
