<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiItemsController extends Controller
{    
    
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'price'      => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'image'       =>  'url|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token)->toArray();
        $data = [
            'idUser'    => $payload['sub'],
            'name'  =>  $request['name'],
            'price' =>  $request['price'],
            'description' => $request['description'],
            'image'     => $request['image'],
        ];
        Item::create($data);
    }

    public function list()
    {
        $idUser = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['sub'];
        
        $userItems = Item::where('idUser','=',$idUser)->get(['name','price','description','image','created_at','updated_at']);
        return response()->json($userItems);
    }

    public function listId($id)
    {
        $idUser = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['sub'];
        if (!Item::find($id)){
            return response()->json([
                'message' => 'Invalid itemId'
            ],401);
        }
        $userItems = Item::where('id','=',$id)->where('idUser','=',$idUser)->get(['name','price','description','image','created_at','updated_at']);

        return response()->json($userItems[0]);
    }

    public function search($item)
    {
        $idUser = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['sub'];
        $result = Item::where('name','LIKE','%'.$item.'%')->where('idUser','=',$idUser)->get(['name','price','description','image','created_at','updated_at']);
        return response()->json($result); 
    }

    public function edit(Request $request, $itemId)
    {
        $idUser = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['sub'];
        $item = Item::find($itemId);
        if (!$item){
            return response()->json([
                'message' => 'Invalid itemId'
            ],401);
        }
       $item = Item::where('idUser', $idUser)
                    ->where('id', $itemId)
                    ->update($request->all());
        
        return response()->json([
            "message" => "updated"
        ]); 

    }

    public function delete($id)
    {
        $idUser = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['sub'];
        if (!Item::find($id)){
            return response()->json([
                'message' => 'Invalid itemId'
            ],401);
        }
        Item::where('id','=',$id)->where('idUser','=',$idUser)->delete();

        return response()->json([
            "message" => "updated"
        ]); 
    }
}
