<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Para que funcione lo de las imágenes instalamos paquete : composer require --dev mmo/faker-images
        return [
            'nombre'        =>  $this->faker->image(storage_path().'/app/public/uploads',250 , 250,'cats',false),
            'product_id'    =>  \App\Models\Product::factory(),
        ];
    }
}
