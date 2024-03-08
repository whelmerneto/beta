<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
     /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAccountRequest $request)
    {
        $account = Account::firstOrCreate(['name' => $request->name]);

        return new AccountResource($account);
    }

    public function transfer()
    {
        // TODO criar service para serviço de autorizacao, via httpclient
        $email = "seuemail@example.com";
        $base64Email = base64_encode('whelmer_neto@hotmail.com');

        $url = "https://eo9ggxnfribmy6a.m.pipedream.net/beta-authorizer";
        $data = [
            "sender" => 1,
            "receiver" => 2,
            "amount" => 100
        ];

        $headers = [
            "Authorization: Bearer $base64Email",
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        echo $response;
    }
}
