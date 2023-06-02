<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Boards;

class ApiListController extends Controller
{
    function getlist($id){
        $board = Boards::find($id);
        return response()->json($board, 200);
    }
    function postlist(Request $req){
        // 유효성 체크 필요
        $boards = new Boards([
            'title' => $req->title
            ,'content'=>$req->content
        ]);
        $boards->save();

        $arr['errorcode'] ='0';
        $arr['msg'] = 'success';
        $arr['data'] = $boards->only('id','title');

        return $arr;
        
    }

    function putlist(Request $req, $id){
        // 유효성검사
        $rules=array(
            'title' => 'required |between:3,30'
            ,'content' => 'required |max:1000'
        );
        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return $validator->errors()->all();
        }else{
            $result = Boards::find($id);
            $result->title = $req->title;
            $result->content = $req->content;
            $result->save();

            $arr[] = $result->only('id','title','content');
            return $arr;
        }
    }

    function deletelist($id){
        $arrData =[
            'code' => '0'
            ,'msg' => ''
        ];
        $data['id']= $id;
        $validator = Validator::make($data,[
            'id'=> 'required|integer|exists:boards,id'
        ]);
        if($validator->fails()){
            $arrData['code'] = 'E01';
            $arrData['msg'] = 'Error';
            $arrData['errmsg'] = 'id not found';
        }else{
            $board = Boards::find($id);
            if($board){
                $board->delete();
                $arrData['code'] = '0';
                $arrData['msg'] = 'Success';
            }else{
                $arrData['code'] = 'E02';
                $arrData['msg'] = 'Already Deleted';
            }
        }
        return $arrData;
    }
}
