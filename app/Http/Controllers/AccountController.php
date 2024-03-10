<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFundsRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    private $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function store(CreateAccountRequest $request): AccountResource
    {
        $account = Account::firstOrCreate(['name' => $request->name]);

        return new AccountResource($account);
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        $transferInfo = $request->validated();

        return $this->accountService->transfer($transferInfo);
    }

    public function addFunds(AddFundsRequest $request)
    {
        $transferInfo = $request->validated();
        $account = Account::find($transferInfo['account_id']);
        $this->accountService->addFunds($account, $transferInfo["amount"]);
        // incluir o campo opcional no resource igual fiz no lead
        return new AccountResource($account);
    }
}
