<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @coversNothing
 */
class ItemsNoAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_not_authenticated_cant_list_items()
    {
        $response = $this->getJson('api/items/list');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_not_auth_cant_search_using_item_name()
    {
        $response = $this->getJson('api/items/list/sanduiche');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_not_authenticated_cant_list_specific_item()
    {
        $response = $this->getJson('api/items/list/1');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_not_authenticated_cant_edit_an_item()
    {
        $item = Item::factory()->create();
        $itemAfterEdit = [
            'name' => 'Porção de Frango',
            'price' => 40.50,
            'description' => 'Lorem Ipsum',
            'image' => 'https://imagemDeComida.jpeg',
        ];

        $response = $this->putJson('api/items/edit/'.$item->id, $itemAfterEdit);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_not_authenticated_cant_delete_an_item()
    {
        $item = Item::factory()->create();

        $response = $this->deleteJson('api/items/delete/'.$item->id);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_not_authenticated_cant_create_item()
    {
        $item = Item::factory()->create();

        $response = $this->postJson('api/items/create', $item->toArray());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
