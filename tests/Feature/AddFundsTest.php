<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AddFundsTest extends TestCase
{
//    use RefreshDatabase;

    public function test_can_add_funds_to_account()
    {
        // Crie uma conta fictícia para o teste
        $account = Account::factory()->create(['balance' => 1000]);

        // Faça uma requisição para adicionar fundos à conta criada
        $response = $this->postJson('/api/account/addFunds', [
            'account_id' => $account->id,
            'amount' => 500
        ]);

        // Verifique se a resposta foi bem sucedida (status 200)
        $response->assertStatus(200);

        // Verifique se os fundos foram adicionados corretamente à conta
        $this->assertEquals(1500, $account->fresh()->balance);
    }

    public function test_cannot_add_funds_to_invalid_account()
    {
        // Tente adicionar fundos a uma conta inexistente (ID inválido)
        $response = $this->postJson('/api/account/addFunds', [
            'account_id' => 9999,
            'amount' => 500
        ]);

        // Verifique se a resposta foi uma falha (status 404)
        $response->assertStatus(422);
    }

    public function test_amount_must_be_greater_than_zero()
    {
        // Crie uma conta fictícia para o teste
        $account = Account::factory()->create(['balance' => 1000]);

        // Tente adicionar um valor inválido (zero) à conta
        $response = $this->postJson('/api/account/addFunds', [
            'account_id' => $account->id,
            'amount' => 0
        ]);

        // Verifique se a resposta foi uma falha (status 422 - Unprocessable Entity)
        $response->assertStatus(422);
    }
}
