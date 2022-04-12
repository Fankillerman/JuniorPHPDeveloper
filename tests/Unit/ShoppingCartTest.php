<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingCartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_shopping_cart_can_be_rendered()
    {
        $response = $this->get(route('cart'));
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
    }

    public function test_page_with_products_can_be_rendered()
    {
        $response = $this->get(route('products'));
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
    }

    public function test_db_has_products_in_db()
    {
        Product::factory(20)->create();

        $this->assertTrue(Product::count() > 0);

    }

    public function test_product_is_visible()
    {
        $product = Product::factory()->create();
        $response = $this->get('/');

        $response->assertSee($product->name);
        $response->assertSee($product->price);

    }

    public function test_product_is_not_visible()
    {
        Product::factory()->create();
        $response = $this->get('/');

        $response->assertDontSee('dsadsadasdsa');
    }

    public function test_product_can_be_added_to_cart()
    {
         Product::factory()->create();

        $response = $this->get(route('add.to.cart', ['id' => 1]));
        $response->assertStatus(302);

    }

    public function test_product_can_not_be_added_to_cart()
    {
        Product::factory(20)->create();

        $response = $this->get(route('add.to.cart', ['id' => 'dasdasdsadas']));
        $response->assertStatus(404);

    }

    public function test_product_can_be_twice_added()
    {
        Product::factory(20)->create();

        $response = $this->get(route('add.to.cart', ['id' => 1]));
        $response = $this->get(route('add.to.cart', ['id' => 1]));
        $response = $this->get(route('cart'));
        $response->assertSee(2);

        $response->assertStatus(200);

    }

    public function test_product_can_be_deleted()
    {
        Product::factory(20)->create();

        $response = $this->get(route('add.to.cart', ['id' => 1]));
        for ($i = 2; $i <= 60; $i++) {
            $response = $this->get(route('add.to.cart', ['id' => 1]));

        }
        $response = $this->get(route('cart'));
        $response->assertStatus(200);

        $response->assertSee(60);


        for ($i = 0; $i <= 30; $i++) {
            $response = $this->get(route('remove.from.cart', ['id' => 1]));

        }
        $response->assertSee('30');


    }

    public function test_product_quantity_can_be_updated()
    {
        Product::factory(20)->create();
        $response = $this->get(route('add.to.cart', ['id' => 1]));
        $response = $this->get(route('cart'));
        $response->assertSee(1);

        $response->assertStatus(200);
        $response = $this->patch(route('update.cart'), [
            'id' => 1,
            'quantity' => 1,
        ]);
        $response = $this->get(route('cart'));
        $response->assertSee(2);

        $response->assertStatus(200);


    }

}
