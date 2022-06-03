<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $this->faker->addProvider(new \FakerRestaurant\Provider\pt_BR\Restaurant($this->faker));

        return [
            'idUser' => User::factory(),
            'name' => $this->faker->foodName(),
            'price' => $this->faker->randomNumber(2),
            'description' => 'Lorem ipsum',
            'image' => 'https://imagemDeComida.jpeg',
        ];
    }
}
