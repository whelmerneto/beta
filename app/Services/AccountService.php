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
    public function transfer(array $dados) {
        $sender = Account::find($dados['sender_id']);
        $receiver = Account::find($dados['receiver_id']);

        if ($sender->balance > 0 && $sender->balance >= $dados['amount']) {
            $authorized = $this->betaAuthorizerService->authorize($sender->id, $receiver->id, $dados['amount']);

            if ($authorized['authorized']) {
                if ($dados['scheduled']) {
                    return $this->scheduleTransaction($sender, $receiver, $dados["amount"], $dados["schedule_date"] );
                }

                return $this->directTransfer($sender, $receiver, $dados["amount"]);
            } else {
                return response()->json(["success" => false, "msg" => "Transacao nao autorizada."]);
            }
        }

        return response()->json(["success" => false, "msg" => "Remetente não possui saldo o suficiente para realizar transacao."]);
    }

    private function scheduleTransaction(Account $sender, Account $receiver, float $amount, string $scheduleDate)
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

    private function directTransfer(Account $sender, Account $receiver, float $amount)
    {
        $this->updateBalance($sender, $receiver, $amount);
        return Transactions::create([
            "sender_id" => $sender->id,
            "receiver_id" => $receiver->id,
            "amount" => $amount,
            "scheduled" => false,
            "sent" => true
        ]);
    }

    public function sendScheduledTransaction(Transactions $transaction)
    {
        $sender = Account::find($transaction->sender_id);
        $receiver = Account::find($transaction->receiver_id);
        $this->updateBalance($sender, $receiver, $transaction->amount);

        return $transaction->update(['sent' => true]);
    }

    public function addFunds($transactionInfo)
    {
        return Account::find($transactionInfo->account_id)->update(['balance' => $transactionInfo->amount]);
    }

    private function updateBalance(Account $sender, Account  $receiver, float $amount): void
    {
        $sender->update(['balance' => $sender->balance - $amount]);
        $receiver->update(['balance' => $receiver->balance + $amount]);
    }
}
