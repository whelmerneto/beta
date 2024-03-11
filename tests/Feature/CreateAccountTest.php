<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateAccountTest extends TestCase
{
    use RefreshDatabase; // banco de dados reiniciado após cada teste

    /**
     * Testa a criação de uma conta.
     *
     * @return void
     */
    public function testCreateAccount()
    {
        $response = $this->postJson('/api/account/create', [
            'name' => 'Conta Teste',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('accounts', [
            'name' => 'Conta Teste',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'balance'
            ],
        ]);
    }

    public function testCreateAccountWithInvalidData()
    {
        // Solicitação com dados inválidos (vazio)
        $response = $this->postJson('/api/account/create', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Verificar se a resposta contém uma mensagem de erro para o campo 'name'
        $response->assertJsonValidationErrors('name');
        // Enviar uma solicitação com dados inválidos (nome muito longo)
        $response = $this->postJson('/api/account/create', [
            'name' => str_repeat('A', 256), // Supondo que o limite seja 255 caracteres
        ]);

        // Verificar se a resposta HTTP é 422
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        // Verificar se a resposta contém uma mensagem de erro para o campo 'name'
        $response->assertJsonValidationErrors('name');
    }
}
