<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFundsRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\Transactions;
use App\Services\AccountService;
use App\Services\BetaAuthorizerService;

class AccountController extends Controller
{
    private $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function store(CreateAccountRequest $request)
    {
        $account = Account::firstOrCreate(['name' => $request->name]);

        return new AccountResource($account);
    }

    public function transfer(TransferRequest $request)
    {
        $transferInfo = $request->validated();

        return $this->accountService->transfer($transferInfo);
    }

    public function addFunds(AddFundsRequest $request)
    {
        $transferInfo = $request->validated();
        return $this->accountService->addFunds($transferInfo);
    }
}
