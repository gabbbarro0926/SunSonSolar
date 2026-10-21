<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use App\Models\BookingModel;
use App\Models\PaymentModel;
use App\Models\UserModel;

/**
 * Reports & analytics. Output is chart-ready ({labels, values}).
 * Aggregation is done in PHP so it works on both MySQL and SQLite.
 */
class ReportController extends BaseApiController
{
    private function range(): array
    {
        $from = $this->request->getGet('from') ?: date('Y-m-01', strtotime('-5 months'));
        $to   = $this->request->getGet('to') ?: date('Y-m-d');

        return [$from . ' 00:00:00', $to . ' 23:59:59'];
    }

    /** GET /api/admin/reports/summary?from=&to= */
    public function summary()
    {
        [$from, $to] = $this->range();

        $bookings = (new BookingModel())->where('created_at >=', $from)->where('created_at <=', $to)->findAll();
        $byStatus = array_count_values(array_column($bookings, 'status'));

        $revenue = array_sum(array_column(
            (new PaymentModel())->where('status', 'paid')->where('paid_at >=', $from)->where('paid_at <=', $to)->findAll(),
            'amount'
        ));

        return $this->ok([
            'range'              => ['from' => substr($from, 0, 10), 'to' => substr($to, 0, 10)],
            'total_customers'    => (new UserModel())->where('user_type', 'customer')->countAllResults(),
            'total_employees'    => (new UserModel())->where('user_type', 'employee')->countAllResults(),
            'bookings_in_range'  => count($bookings),
            'bookings_by_status' => $byStatus,
            'revenue_in_range'   => round((float) $revenue, 2),
        ]);
    }

    /** GET /api/admin/reports/customer-activity — new sign-ups and bookings per month */
    public function customerActivity()
    {
        [$from, $to] = $this->range();

        $signups  = (new UserModel())->select('created_at')->where('user_type', 'customer')
            ->where('created_at >=', $from)->where('created_at <=', $to)->findAll();
        $bookings = (new BookingModel())->select('created_at')
            ->where('created_at >=', $from)->where('created_at <=', $to)->findAll();

        $s = $this->perMonth($signups, 'created_at');
        $b = $this->perMonth($bookings, 'created_at');
        $labels = array_values(array_unique(array_merge(array_keys($s), array_keys($b))));
        sort($labels);

        return $this->ok([
            'labels'   => $labels,
            'signups'  => array_map(fn ($l) => $s[$l] ?? 0, $labels),
            'bookings' => array_map(fn ($l) => $b[$l] ?? 0, $labels),
        ]);
    }

    /** GET /api/admin/reports/service-usage — bookings per service */
    public function serviceUsage()
    {
        [$from, $to] = $this->range();

        $rows = (new BookingModel())
            ->select('services.name AS service, bookings.status')
            ->join('services', 'services.id = bookings.service_id')
            ->where('bookings.scheduled_at >=', $from)->where('bookings.scheduled_at <=', $to)
            ->whereNotIn('bookings.status', ['declined', 'cancelled'])->findAll();

        $counts = array_count_values(array_column($rows, 'service'));
        arsort($counts);

        return $this->ok(['labels' => array_keys($counts), 'values' => array_values($counts)]);
    }

    /** GET /api/admin/reports/revenue — paid revenue per month */
    public function revenue()
    {
        [$from, $to] = $this->range();

        $rows = (new PaymentModel())->select('amount, paid_at')->where('status', 'paid')
            ->where('paid_at >=', $from)->where('paid_at <=', $to)->findAll();

        $totals = [];
        foreach ($rows as $r) {
            $k = substr($r['paid_at'], 0, 7);
            $totals[$k] = ($totals[$k] ?? 0) + (float) $r['amount'];
        }
        ksort($totals);

        return $this->ok([
            'labels'  => array_keys($totals),
            'values'  => array_map(fn ($v) => round($v, 2), array_values($totals)),
            'total'   => round(array_sum($totals), 2),
        ]);
    }

    private function perMonth(array $rows, string $col): array
    {
        $out = [];
        foreach ($rows as $r) {
            $k = substr($r[$col], 0, 7);
            $out[$k] = ($out[$k] ?? 0) + 1;
        }
        ksort($out);

        return $out;
    }
}
