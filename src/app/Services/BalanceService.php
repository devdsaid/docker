<?php

namespace App\Services;

use App\Models\User;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class BalanceService
{
    public function getBalance(int $userId): array
    {
        $user = User::find($userId);

        if (!$user) {
            throw new Exception('User not found', 404);
        }

        $balance = Balance::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0]
        );

        return [
            'user_id' => $userId,
            'balance' => (float) $balance->balance
        ];
    }

    public function deposit(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::find($data['user_id']);

            if (!$user) {
                throw new Exception('User not found', 404);
            }

            if ($data['amount'] <= 0) {
                throw new Exception('Amount must be greater than 0', 422);
            }

            $balance = Balance::firstOrCreate(
                ['user_id' => $data['user_id']],
                ['balance' => 0]
            );

            $balance->increment('balance', $data['amount']);

            Transaction::create([
                'user_id' => $data['user_id'],
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => $data['amount'],
                'comment' => $data['comment'] ?? null
            ]);

            return [
                'user_id' => $data['user_id'],
                'balance' => (float) $balance->fresh()->balance
            ];
        });
    }

    public function withdraw(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::find($data['user_id']);

            if (!$user) {
                throw new Exception('User not found', 404);
            }

            if ($data['amount'] <= 0) {
                throw new Exception('Amount must be greater than 0', 422);
            }

            $balance = Balance::firstOrCreate(
                ['user_id' => $data['user_id']],
                ['balance' => 0]
            );

            if (!$balance->hasSufficientFunds($data['amount'])) {
                throw new Exception('Insufficient funds', 409);
            }

            $balance->decrement('balance', $data['amount']);

            Transaction::create([
                'user_id' => $data['user_id'],
                'type' => Transaction::TYPE_WITHDRAW,
                'amount' => $data['amount'],
                'comment' => $data['comment'] ?? null
            ]);

            return [
                'user_id' => $data['user_id'],
                'balance' => (float) $balance->fresh()->balance
            ];
        });
    }

    public function transfer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $fromUser = User::find($data['from_user_id']);
            $toUser = User::find($data['to_user_id']);

            if (!$fromUser) {
                throw new Exception('Sender user not found', 404);
            }

            if (!$toUser) {
                throw new Exception('Recipient user not found', 404);
            }

            if ($data['from_user_id'] === $data['to_user_id']) {
                throw new Exception('Cannot transfer', 422);
            }

            if ($data['amount'] <= 0) {
                throw new Exception('Amount must 0', 422);
            }

            $fromBalance = Balance::firstOrCreate(
                ['user_id' => $data['from_user_id']],
                ['balance' => 0]
            );

            if (!$fromBalance->hasSufficientFunds($data['amount'])) {
                throw new Exception('Insufficien', 409);
            }

            $fromBalance->decrement('balance', $data['amount']);

            $toBalance = Balance::firstOrCreate(
                ['user_id' => $data['to_user_id']],
                ['balance' => 0]
            );
            $toBalance->increment('balance', $data['amount']);

            Transaction::create([
                'user_id' => $data['from_user_id'],
                'type' => Transaction::TYPE_TRANSFER_OUT,
                'amount' => $data['amount'],
                'comment' => $data['comment'] ?? null,
                'related_user_id' => $data['to_user_id']
            ]);

            Transaction::create([
                'user_id' => $data['to_user_id'],
                'type' => Transaction::TYPE_TRANSFER_IN,
                'amount' => $data['amount'],
                'comment' => $data['comment'] ?? null,
                'related_user_id' => $data['from_user_id']
            ]);

            return [
                'from_user_id' => $data['from_user_id'],
                'to_user_id' => $data['to_user_id'],
                'amount' => $data['amount'],
                'from_user_balance' => (float) $fromBalance->fresh()->balance,
                'to_user_balance' => (float) $toBalance->fresh()->balance
            ];
        });
    }
}
