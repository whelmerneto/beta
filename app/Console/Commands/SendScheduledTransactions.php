<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Transactions;
use App\Services\AccountService;

class SendScheduledTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-scheduled-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando cadastrado para rodar todos os dias as 05h. Dedicado a executar todas as transacoes agendadas no dia.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $registeredTransactions = Transactions::where('sent', '=', false)->get();
        $accountService = app(AccountService::class);

        foreach($registeredTransactions as $transaction) {
            if (Carbon::parse($transaction->schedule_date)->isToday()) {
                $accountService->sendScheduledTransaction($transaction);
                $this->info("Transação $transaction->id realizada com sucesso");
            }
        }
        $this->info("Todas as transações do dia " . Carbon::today()->format('Y-m-d') . " foram realizadas");
    }
}
