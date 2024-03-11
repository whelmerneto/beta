<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Account;
use Illuminate\Http\Response;

class TransferTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Testa a transferência de fundos com dados válidos.
     *
     * @return void
     */
    public function testTransferWithValidData()
    {
        $sender = Account::factory()->create();
        $receiver = Account::factory()->create();

        // Simular uma transferência de 100 unidades do remetente para o destinatário
        $response = $this->postJson('/api/account/transfer', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => 100,
            'scheduled' => false,
        ]);
        if (isset($response["authorized"]) && !$response["authorized"]) {
            $response->assertStatus(Response::HTTP_BAD_REQUEST);
        }
        // Status 200
        $response->assertStatus(Response::HTTP_OK);

        // Buscar no banco
        $this->assertDatabaseHas('transactions', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => 100,
            'scheduled' => false,
            'sent' => true,
        ]);
    }

    /**
     * Testa a falha na transferência de fundos devido a dados inválidos.
     *
     * @return void
     */
    public function testTransferWithInvalidData()
    {
        // Enviar uma solicitação com corpo vazio
        $response = $this->postJson('/api/account/transfer', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->postJson('/api/account/transfer', [
            'sender_id' => rand(1000, 3000),
            'receiver_id' => 8888,
            'amount' => 100,
            'scheduled' => false,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
