<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiItemsController extends Controller
{   
    /**
    * @OA\Post(
    *   path="/api/items/create",
    *   tags={"Items"},
    *   security={{"bearerAuth":{} }},
    *   description="Endpoint for adding a new item",
    *   @OA\RequestBody(
    *       required = true,
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *               property="name",
    *               type = "string",
    *               maxLength=255,
    *               example="Pastel de frango", 
    *           ),
    *            @OA\Property(
    *               property="price",
    *               type = "numeric",
    *               example="10.50", 
    *           ),
    *            @OA\Property(
    *               property="description",
    *               type = "string",
    *               maxLength =255,
    *               example="Pastel de frango com catupiry", 
    *           ),
    *           @OA\Property(
    *               property="image",
    *               type = "url",
    *               example="https://imagens/pastel_de_frango.jpeg", 
    *           ),
    *        ),
    *    ),
    *
    *   @OA\Response(response="201", description="New item created"),
    *   @OA\Response(response="400", description="Invalid data"),
    *   @OA\Response(response="401", description="Invalid Token"),
    *
    *   
    * ),
    *   @param Request $request
    *   @return JsonResponse
    */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'price'      => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'image'       =>  'url|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
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
        return response()->json(["message" => "created"], 201);
    }
    /**
    * @OA\Get(
    *   path="/api/items/list",
    *   tags={"Items"},
    *   security={{"bearerAuth":{} }},
    *   description="Endpoint for listing all user's menu items",
    *   
    *   @OA\Response(response="401", description="Invalid Token"),
    *   @OA\Response(response="200", description="Items in user's menu"),
    *  ),
    *   @return JsonResponse
    */


    public function list()
    {
        $idUser = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['sub'];
        
        $userItems = Item::where('idUser','=',$idUser)->get(['name','price','description','image','created_at','updated_at']);
        return response()->json($userItems);
    }

    /**
    * @OA\Get(
    *   path="/api/items/list/",
    *   tags={"Items"},
    *   security={{"bearerAuth":{} }},
    *   description="Endpoint for listing one item from user's menu",
    *   @OA\Parameter(
    *          parameter="Item id",
    *          name="id",
    *          description="item id to search for the item in the database",
    *          @OA\Schema(
    *              type="integer"
    *          ),
    *          in="path",
    *          required=true,
    *     ),
    *   @OA\Response(response="401", description="Invalid Token"),
    *   @OA\Response(response="400", description="Invalid item id"),
    *   @OA\Response(response="200", description="Item found"),
    *  ),
    *    @param int $id
    *   @return JsonResponse
    *   
    */

    public function listId($id)
    {
        $idUser = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['sub'];
        if (!Item::find($id)){
            return response()->json([
                'message' => 'Invalid itemId'
            ],400);
        }
        $userItems = Item::where('id','=',$id)->where('idUser','=',$idUser)->get(['name','price','description','image','created_at','updated_at']);

        return response()->json($userItems[0]);
    }

    /**
    * @OA\Get(
    *   path="/api/items/search/",
    *   tags={"Items"},
    *   security={{"bearerAuth":{} }},
    *   description="Endpoint for searching an item from user's menu",
    *   @OA\Parameter(
    *          parameter="item name",
    *          name="item",
    *          description="item name to search for the item in the database",
    *          @OA\Schema(
    *              type="string"
    *          ),
    *          in="query",
    *          required=true,
    *     ),
    *   @OA\Response(response="401", description="Invalid Token"),
    *   @OA\Response(response="200", description="Items found"),
    *  ),
    *   @param string $item
    *   @return JsonResponse
    */

    public function search($item)
    {
        $idUser = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['sub'];
        $result = Item::where('name','LIKE','%'.$item.'%')->where('idUser','=',$idUser)->get(['name','price','description','image','created_at','updated_at']);
        return response()->json($result); 
    }

    /**
    * @OA\Put(
    *   path="/api/items/edit/",
    *   tags={"Items"},
    *   security={{"bearerAuth":{} }},
    *   description="Endpoint for editing an item from user's menu",
    *   @OA\Parameter(
    *          parameter="item",
    *          name="id",
    *          description="item name to search for the item in the database",
    *          @OA\Schema(
    *              type="integer"
    *          ),
    *          in="path",
    *          required=true,
    *     ),
    *   @OA\Response(response="401", description="Invalid Token"),
    *   @OA\Response(response="200", description="Items found"),
    *    @OA\Response(response="400", description="Invalid id"),
    *  ),
    *   @param string $id
    *   @param Request $request
    *   @return JsonResponse
    */

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

    /**
    * @OA\Delete(
    *   path="/api/items/delete/",
    *   tags={"Items"},
    *   security={{"bearerAuth":{} }},
    *   description="Endpoint for deleting an item from user's menu",
    *   @OA\Parameter(
    *          parameter="item id",
    *          name="id",
    *          description="item id to search for the item in the database",
    *          @OA\Schema(
    *              type="integer"
    *          ),
    *          in="query",
    *          required=true,
    *     ),
    *   @OA\Response(response="401", description="Invalid Token"),
    *   @OA\Response(response="200", description="Items found"),
    *  ),
    *   @param string $id
    *   @return JsonResponse
    */

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