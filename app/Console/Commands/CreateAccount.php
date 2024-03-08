<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use App\Models\Account;

class CreateAccount extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando utilizado para a criacao de contas via CLI.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $conta = Account::firstOrCreate(
            ['name' => $this->argument('name')]
        );

        if ($conta) {
            $this->info('Conta criada com sucesso');
        }
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => 'Qual o nome do titular da conta?'
        ];
    }
}
