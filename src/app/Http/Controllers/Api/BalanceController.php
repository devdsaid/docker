<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Requests\TransferRequest;
use App\Services\BalanceService;
use App\Traits\ApiResponse;
use Exception;

class BalanceController extends Controller
{
    use ApiResponse;

    public function __construct(private BalanceService $balanceService) {}

    public function getBalance(int $userId)
    {
        try {
            $result = $this->balanceService->getBalance($userId);
            return $this->success($result);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function deposit(DepositRequest $request)
    {
        try {
            $result = $this->balanceService->deposit($request->validated());
            return $this->success($result, 'Deposit successful');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function withdraw(WithdrawRequest $request)
    {
        try {
            $result = $this->balanceService->withdraw($request->validated());
            return $this->success($result, 'Withdrawal successful');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function transfer(TransferRequest $request)
    {
        try {
            $result = $this->balanceService->transfer($request->validated());
            return $this->success($result, 'Transfer successful');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
