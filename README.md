## CLI command para criação de conta:
php artisan account:create {nome}

O nome é obrigatório, caso não passe junto ao comando, o CLI ira pedir por um nome.
A conta começa com o saldo 0 como padrão. Existe uma rota (api/account/addFunds) para adicionar fundos à uma conta.

## Rotas
Existe uma Collection na raiz do projeto, que contem requests para as rotas abaixo:

### POST api/account/create 
Rota para a criação de contas

Body:
{
    "name" : "Whelmer"
}

### POST api/account/addFunds
Rota para adicionar saldo em uma conta

Body:
{
    "account_id" : 1,
    "amount" : 2000
}

### POST api/account/transfer
Rota para realizar transferencias entre duas contas

Body:
{
    "sender_id" : 1,
    "receiver_id" : 2,{
    "account_id" : 1,
    "amount" : 2000
}
    "amount" :100,
    "scheduled": true,
    "schedule_date" : "2024-03-09"
}

