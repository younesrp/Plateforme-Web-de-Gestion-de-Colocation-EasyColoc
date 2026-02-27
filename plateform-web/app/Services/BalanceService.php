<?php

namespace App\Services;

use App\Models\Colocation;
use Illuminate\Support\Collection;

class BalanceService
{
    public function calculateBalances(Colocation $colocation): array
    {
        $members = $colocation->activeMembers;
        $expenses = $colocation->expenses;
        $payments = $colocation->payments;

        if ($members->isEmpty() || $expenses->isEmpty()) {
            return [
                'balances' => [],
                'settlements' => [],
                'total' => 0,
            ];
        }

        $totalExpenses = $expenses->sum('amount');
        $sharePerMember = $totalExpenses / $members->count();

        $balances = [];
        foreach ($members as $member) {
            $totalPaid = $expenses->where('payer_id', $member->id)->sum('amount');
            
            $paymentsReceived = $payments->where('to_user_id', $member->id)->sum('amount');
            $paymentsSent = $payments->where('from_user_id', $member->id)->sum('amount');
            
            $balance = $totalPaid - $sharePerMember + $paymentsReceived - $paymentsSent;

            $balances[$member->id] = [
                'user' => $member,
                'total_paid' => $totalPaid,
                'share' => $sharePerMember,
                'balance' => $balance,
            ];
        }

        $settlements = $this->simplifyDebts($balances);

        return [
            'balances' => $balances,
            'settlements' => $settlements,
            'total' => $totalExpenses,
        ];
    }

    private function simplifyDebts(array $balances): array
    {
        $creditors = [];
        $debtors = [];

        foreach ($balances as $userId => $data) {
            if ($data['balance'] > 0.01) {
                $creditors[] = ['user' => $data['user'], 'amount' => $data['balance']];
            } elseif ($data['balance'] < -0.01) {
                $debtors[] = ['user' => $data['user'], 'amount' => abs($data['balance'])];
            }
        }

        usort($creditors, fn($a, $b) => $b['amount'] <=> $a['amount']);
        usort($debtors, fn($a, $b) => $b['amount'] <=> $a['amount']);

        $settlements = [];
        $i = 0;
        $j = 0;

        while ($i < count($creditors) && $j < count($debtors)) {
            $creditor = &$creditors[$i];
            $debtor = &$debtors[$j];

            $amount = min($creditor['amount'], $debtor['amount']);

            if ($amount > 0.01) {
                $settlements[] = [
                    'from' => $debtor['user'],
                    'to' => $creditor['user'],
                    'amount' => round($amount, 2),
                ];
            }

            $creditor['amount'] -= $amount;
            $debtor['amount'] -= $amount;

            if ($creditor['amount'] < 0.01) {
                $i++;
            }
            if ($debtor['amount'] < 0.01) {
                $j++;
            }
        }

        return $settlements;
    }
}
