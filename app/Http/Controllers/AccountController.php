<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFundsRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    private $accountService;

    /**
     * @param AccountService $accountService
     */
    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * @param CreateAccountRequest $request
     * @return AccountResource
     */
    public function store(CreateAccountRequest $request): AccountResource
    {
        $account = Account::Create(['name' => $request->name]);

        return new AccountResource($account);
    }

    /**
     * @param TransferRequest $request
     * @return JsonResponse
     */
    public function transfer(TransferRequest $request): JsonResponse|TransactionResource
    {
        $transferInfo = $request->validated();
        $transaction = $this->accountService->transfer($transferInfo);

        if(isset($transaction["authorized"]) && !$transaction["authorized"]) {
            return response()->json($transaction, 400);
        }

        return new TransactionResource($transaction);
    }

    /**
     * @param AddFundsRequest $request
     * @return AccountResource
     */
    public function addFunds(AddFundsRequest $request): AccountResource
    {
        $transferInfo = $request->validated();
        $account = Account::find($transferInfo['account_id']);
        $this->accountService->addFunds($account, $transferInfo["amount"]);

        return new AccountResource($account);
    }
}
