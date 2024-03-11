<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\BetaAuthorizerService;

class AccountServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testTransferWithSufficientFunds()
    {
        $sender = Account::factory()->create();
        $receiver = Account::factory()->create();

        // Dados de transferência
        $transferData = [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => 500,
            'scheduled' => false,
        ];

        $accountService = new AccountService(new BetaAuthorizerService());
        $result = $accountService->transfer($transferData);

        // Verificando os saldos atualizados
        if(!isset($result["authorized"])) {
            $this->assertGreaterThan(500, $sender->balance);
            $this->assertGreaterThan(500, $receiver->balance);

            $this->assertDatabaseHas('transactions', [
                'id' => $result['id'],
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => 500,
                'scheduled' => false,
                'sent' => true,
            ]);
        } else {
            $this->assertFalse($result["authorized"]);
        }
    }

    public function testTransferWithInsufficientFunds()
    {
        $sender = Account::factory()->create();
        $receiver = Account::factory()->create();

        // Dados de transferência
        $transferData = [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => 20000,
            'scheduled' => false,
        ];

        $accountService = new AccountService(new BetaAuthorizerService());

        $result = $accountService->transfer($transferData);

        if (isset($result["authorized"])) {
            $this->assertFalse($result['authorized']);
        }
    }
}

