<?php

namespace App\Actions;

use App\Models\Business;
use App\Models\PriceSimulation;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use LogicException;

class GetDashboardData
{
    /**
     * @return array{
     *     period: array{from: string, to: string},
     *     kpis: array{
     *         product_count: int,
     *         average_hpp: ?string,
     *         average_recommended_price: ?string,
     *         average_selling_price: ?string,
     *         average_margin_percent: ?string,
     *         average_material_cost_per_batch: ?string,
     *         average_labor_cost_per_batch: ?string,
     *         average_overhead_cost_per_batch: ?string
     *     },
     *     trends: list<array{
     *         month: string,
     *         average_hpp: ?string,
     *         average_simulated_price: ?string,
     *         average_projected_profit: ?string,
     *         average_margin_percent: ?string,
     *         simulation_count: int
     *     }>
     * }
     */
    public function handle(Business $business): array
    {
        $now = now();
        $periodStart = $now->copy()->startOfMonth()->subMonths(11);
        $productAggregates = $business->products()
            ->selectRaw('
                COUNT(*) as product_count,
                AVG(calculated_hpp) as average_hpp,
                AVG(recommended_price) as average_recommended_price,
                AVG(selling_price) as average_selling_price,
                AVG(
                    CASE
                        WHEN selling_price > 0
                        THEN (selling_price - calculated_hpp) * 100.0 / selling_price
                    END
                ) as average_margin_percent,
                AVG(COALESCE((
                    SELECT SUM(product_materials.subtotal_cost)
                    FROM product_materials
                    WHERE product_materials.product_id = products.id
                ), 0)) as average_material_cost_per_batch,
                AVG(COALESCE((
                    SELECT SUM(product_labor_costs.subtotal_cost)
                    FROM product_labor_costs
                    WHERE product_labor_costs.product_id = products.id
                ), 0)) as average_labor_cost_per_batch,
                AVG(COALESCE((
                    SELECT SUM(product_overhead_costs.subtotal_cost)
                    FROM product_overhead_costs
                    WHERE product_overhead_costs.product_id = products.id
                ), 0)) as average_overhead_cost_per_batch
            ')
            ->firstOrFail();
        $monthExpression = match (DB::connection()->getDriverName()) {
            'mysql' => "DATE_FORMAT(price_simulations.created_at, '%Y-%m')",
            'sqlite' => "strftime('%Y-%m', price_simulations.created_at)",
            default => throw new LogicException('Dashboard trends support MySQL and SQLite connections.'),
        };
        $monthlyAggregates = PriceSimulation::query()
            ->join('products', 'products.id', '=', 'price_simulations.product_id')
            ->where('products.business_id', $business->id)
            ->whereBetween('price_simulations.created_at', [$periodStart, $now])
            ->whereNotNull('price_simulations.hpp_snapshot')
            ->where('price_simulations.simulated_price', '>', 0)
            ->selectRaw("
                {$monthExpression} as month,
                AVG(price_simulations.hpp_snapshot) as average_hpp,
                AVG(price_simulations.simulated_price) as average_simulated_price,
                AVG(
                    (price_simulations.simulated_price - price_simulations.hpp_snapshot)
                    * 100.0 / price_simulations.simulated_price
                ) as average_margin_percent,
                AVG(price_simulations.projected_profit) as average_projected_profit,
                COUNT(*) as simulation_count
            ")
            ->groupByRaw($monthExpression)
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $trends = [];

        for ($monthOffset = 0; $monthOffset < 12; $monthOffset++) {
            $month = $periodStart->copy()->addMonths($monthOffset)->format('Y-m');
            $aggregates = $monthlyAggregates->get($month);

            $trends[] = [
                'month' => $month,
                'average_hpp' => $this->formatAverage($aggregates?->average_hpp),
                'average_simulated_price' => $this->formatAverage($aggregates?->average_simulated_price),
                'average_projected_profit' => $this->formatAverage($aggregates?->average_projected_profit),
                'average_margin_percent' => $this->formatAverage($aggregates?->average_margin_percent),
                'simulation_count' => (int) ($aggregates?->simulation_count ?? 0),
            ];
        }

        return [
            'period' => [
                'from' => $periodStart->toDateString(),
                'to' => $now->toDateString(),
            ],
            'kpis' => [
                'product_count' => (int) $productAggregates->product_count,
                'average_hpp' => $this->formatAverage($productAggregates->average_hpp),
                'average_recommended_price' => $this->formatAverage($productAggregates->average_recommended_price),
                'average_selling_price' => $this->formatAverage($productAggregates->average_selling_price),
                'average_margin_percent' => $this->formatAverage($productAggregates->average_margin_percent),
                'average_material_cost_per_batch' => $this->formatAverage($productAggregates->average_material_cost_per_batch),
                'average_labor_cost_per_batch' => $this->formatAverage($productAggregates->average_labor_cost_per_batch),
                'average_overhead_cost_per_batch' => $this->formatAverage($productAggregates->average_overhead_cost_per_batch),
            ],
            'trends' => $trends,
        ];
    }

    private function formatAverage(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return BigDecimal::of((string) $value)
            ->toScale(2, RoundingMode::HalfUp)
            ->toString();
    }
}
