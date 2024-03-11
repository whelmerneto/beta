<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transactions;
use Carbon\Carbon;

Class AccountService
{
    private $betaAuthorizerService;
    public function __construct(BetaAuthorizerService $betaAuthorizerService)
    {
        $this->betaAuthorizerService = $betaAuthorizerService;
    }

    /**
     * @param array $dados
     * @return array|Transactions
     */
    public function transfer(array $dados):  array|Transactions
    {
        $sender = Account::find($dados['sender_id']);
        $receiver = Account::find($dados['receiver_id']);

        if ($this->checkFunds($sender, $dados["amount"])) {
            $authorizationApi = $this->betaAuthorizerService->authorize($sender->id, $receiver->id, $dados['amount']);

            if (!$authorizationApi['authorized']) {
                return $authorizationApi;
            }

            if ($dados['scheduled']) {
                return $this->scheduleTransaction($sender, $receiver, $dados["amount"], $dados["schedule_date"] );
            }

            return $this->directTransfer($sender, $receiver, $dados["amount"]);
        }

        //  Caso nao tenha saldo disponivel
        return [
            'authorized' => false,
            'msg' => 'Saldo insuficiente.'
        ];
    }

    /**
     * @param Account $sender
     * @param Account $receiver
     * @param float $amount
     * @param string $scheduleDate
     * @return Transactions
     */
    private function scheduleTransaction(Account $sender, Account $receiver, float $amount, string $scheduleDate): Transactions
    {
        return Transactions::create([
            "sender_id" => $sender->id,
            "receiver_id" => $receiver->id,
            "amount" => $amount,
            "scheduled" => true,
            "schedule_date" => Carbon::parse($scheduleDate)->format('Y-m-d'),
            "sent" => false
        ]);
    }

    /**
     * @param Account $sender
     * @param Account $receiver
     * @param float $amount
     * @return mixed
     */
    private function directTransfer(Account $sender, Account $receiver, float $amount)
    {
        $this->updateBalance($sender, $receiver, $amount);
        return Transactions::Create([
            "sender_id" => $sender->id,
            "receiver_id" => $receiver->id,
            "amount" => $amount,
            "scheduled" => false,
            "sent" => true
        ]);
    }

    /**
     * @param Transactions $transaction
     * @return bool
     */
    public function sendScheduledTransaction(Transactions $transaction): bool
    {
        $sender = Account::find($transaction->sender_id);
        $receiver = Account::find($transaction->receiver_id);
        $this->updateBalance($sender, $receiver, $transaction->amount);

        return $transaction->update(['sent' => true]);
    }

    /**
     * @param Account $account
     * @param $amount
     * @return bool
     */
    public function addFunds(Account $account, $amount): bool
    {
        return $account->update(['balance' => $account->balance + $amount]);
    }

    /**
     * @param Account $sender
     * @param Account $receiver
     * @param float $amount
     * @return void
     */
    private function updateBalance(Account $sender, Account  $receiver, float $amount): void
    {
        $sender->update(['balance' => $sender->balance - $amount]);
        $receiver->update(['balance' => $receiver->balance + $amount]);
    }

    /**
     * @param Account $sender
     * @param float $amount
     * @return bool
     *
     */
    private function checkFunds(Account $sender, float $amount): bool
    {
        if ($sender->balance > 0 && $sender->balance >= $amount) {
            return true;
        }
        return false;
    }
}
