<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;

class ItemsNoAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_not_auth_cant_list_items()
    {   
        $response = $this->getJson('api/items/list');
        $response->assertStatus(401);
    }

    public function test_user_not_auth_cant_search_using_item_name()
    {   
        $response = $this->getJson('api/items/list/sanduiche');
        
        $response->assertStatus(401);
    }

    public function test_user_not_auth_cant_list_specific_item()
    {
        $response = $this->getJson('api/items/list/1');
        $response->assertStatus(401);
    }

    public function test_user_not_auth_cant_edit_an_item()
    {
        $item = Item::factory()->create();
        $itemAfterEdit = [
            "name" => "Porção de Frango",
            "price" => 40.50,
            "description" => "Lorem Ipsum",
            "image" =>  "https://imagemDeComida.jpeg",
        ];
        
        $response = $this->putJson('api/items/edit/'.$item->id,$itemAfterEdit);
        
        $response->assertStatus(401);
    }

    public function test_user_not_auth_cant_delete_an_item()
    {
        $item = Item::factory()->create();
        
        $response = $this->deleteJson('api/items/delete/'.$item->id);
        
        $response->assertStatus(401);
    }

    public function test_user_not_auth_cant_create_item()
    {
        $item = Item::factory()->create();

        $response = $this->postJson('api/items/create',$item->toArray());
        
        $response->assertStatus(401);
    }
}
