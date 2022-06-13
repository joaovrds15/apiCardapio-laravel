<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @coversNothing
 */
class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_auth_can_list_items()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('api/items/list');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_auth_can_search_using_item_name()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['idUser' => $user->id]);

        $response = $this->actingAs($user, 'api')
            ->getJson('api/items/list/'.$item->id);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_auth_can_list_specific_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['idUser' => $user->id]);

        $response = $this->actingAs($user, 'api')
            ->getJson('api/items/list/'.$item->id);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_auth_can_edit_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['idUser' => $user->id]);
        $itemAfterEdit = [
            'name' => 'Porção de Frango',
            'price' => 40.50,
            'description' => 'Lorem Ipsum',
            'image' => 'https://imagemDeComida.jpeg',
        ];

        $response = $this->actingAs($user, 'api')
            ->putJson('api/items/edit/'.$item->id, $itemAfterEdit);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_auth_can_delete_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['idUser' => $user->id]);

        $response = $this->actingAs($user, 'api')
            ->deleteJson('api/items/delete/'.$item->id);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_auth_can_create_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('api/items/create', $item->toArray());

        $response->assertStatus(Response::HTTP_CREATED);
    }
}
