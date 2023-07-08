<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function test_index()
    {
        // Chama a rota que lista os produtos
        $response = $this->get('/api/v1/product');


        // Verifica se a resposta da requisição foi bem-sucedida
        $response->assertStatus(200);

        // Verifica se a resposta contém os produtos esperados
        $response->assertJson(Product::all()->toArray());
    }

    public function test_store()
    {
        // Dados do novo produto a ser criado
        $data = [
            'name' => 'Novo Produto',
            'description' => 'Descrição do novo produto',
            'price' => 9.99,
        ];

        // Chama a rota que cria o produto
        $response = $this->post('/api/v1/products', $data);

        // Verifica se a resposta da requisição foi bem-sucedida
        $response->assertStatus(201);

        // Verifica se o produto foi criado corretamente
        $response->assertJson($data);
    }

    /**
     * Testa a exibição de um produto.
     *
     * @return void
     */
    public function test_show()
    {
        // Cria um produto de exemplo no banco de dados
        $product = Product::factory()->create();

        // Chama a rota que exibe o produto
        $response = $this->get("/api/v1/products/{$product->id}");

        // Verifica se a resposta da requisição foi bem-sucedida
        $response->assertStatus(200);

        // Verifica se a resposta contém o produto esperado
        $response->assertJson($product->toArray());
    }

    /**
     * Testa a atualização de um produto.
     *
     * @return void
     */
    public function test_update()
    {
        // Cria um produto de exemplo no banco de dados
        $product = Product::factory()->create();

        // Novos dados para atualizar o produto
        $data = [
            'name' => 'Novo Nome',
            'description' => 'Nova Descrição',
            'price' => 19.99,
        ];

        // Chama a rota que atualiza o produto
        $response = $this->put("/api/v1/products/{$product->id}", $data);

        // Verifica se a resposta da requisição foi bem-sucedida
        $response->assertStatus(200);

        // Atualiza o objeto do produto com os novos dados
        $product->refresh();

        // Verifica se o produto foi atualizado corretamente
        $this->assertEquals($data['name'], $product->name);
        $this->assertEquals($data['description'], $product->description);
        $this->assertEquals($data['price'], $product->price);
    }

    /**
     * Testa a exclusão de um produto.
     *
     * @return void
     */
    public function test_destroy()
    {
        // Cria um produto de exemplo no banco de dados
        $product = Product::factory()->create();

        // Chama a rota que exclui o produto
        $response = $this->delete("/api/v1/products/{$product->id}");

        // Verifica se a resposta da requisição foi bem-sucedida
        $response->assertStatus(204);

        // Verifica se o produto foi excluído corretamente
        $this->assertNull(Product::find($product->id));
    }
}

