<?php

namespace App\Services;

use App\Models\Opportunity;
use Illuminate\Support\Collection;

class AssessmentMatchingService
{
    /**
     * Compute matches against verified opportunities using hard filters and explainable ranking.
     *
     * @param array $criteria Customer criteria:
     *   - 'budget_range' or 'budget_amount'
     *   - 'state'
     *   - 'district' (optional)
     *   - 'carpet_area_sqft' (optional)
     *   - 'sector' (optional sector slug)
     *   - 'experience' (optional: first_time, existing_retail, corporate_professional, other)
     * @return Collection Collection of matched opportunity arrays with score, reasons, and caveats.
     */
    public function match(array $criteria): Collection
    {
        // Non-negotiable: only evaluate published and verified opportunities
        $opportunities = Opportunity::published()
            ->with(['company', 'category', 'financialBreakdowns', 'locations', 'requirements'])
            ->get();

        $budgetLimits = $this->parseBudgetLimits($criteria['budget_range'] ?? null, $criteria['budget_amount'] ?? null);

        $matches = collect();

        foreach ($opportunities as $opp) {
            $hardFilterResult = $this->evaluateHardFilters($opp, $criteria, $budgetLimits);

            if (! $hardFilterResult['passed']) {
                continue; // Hard filter failed; strictly excluded
            }

            $scoreBreakdown = $this->calculateFitScore($opp, $criteria, $budgetLimits);

            $matches->push([
                'opportunity' => $opp,
                'fit_score' => $scoreBreakdown['total_score'],
                'score_breakdown' => $scoreBreakdown['breakdown'],
                'reasons' => array_merge($hardFilterResult['reasons'], $scoreBreakdown['reasons']),
                'caveats' => array_merge($hardFilterResult['caveats'], $scoreBreakdown['caveats']),
            ]);
        }

        // Sort descending by fit score
        return $matches->sortByDesc('fit_score')->values();
    }

    /**
     * Parse budget criteria into min and max whole INR figures.
     */
    protected function parseBudgetLimits(?string $budgetRange, ?int $budgetAmount): array
    {
        if ($budgetAmount) {
            return ['min' => 0, 'max' => $budgetAmount];
        }

        return match ($budgetRange) {
            'under-5l' => ['min' => 0, 'max' => 500000],
            '5l-15l' => ['min' => 500000, 'max' => 1500000],
            '15l-30l' => ['min' => 1500000, 'max' => 3000000],
            '30l-plus' => ['min' => 3000000, 'max' => PHP_INT_MAX],
            default => ['min' => 0, 'max' => PHP_INT_MAX],
        };
    }

    /**
     * Evaluate hard eligibility filters:
     * - Verified & Published status
     * - Investment feasibility
     * - Territory presence
     */
    protected function evaluateHardFilters(Opportunity $opp, array $criteria, array $budgetLimits): array
    {
        $reasons = [];
        $caveats = [];

        // 1. Verification freshness hard check
        if (! $opp->isVerified()) {
            return ['passed' => false, 'reasons' => [], 'caveats' => ['Unverified opportunity']];
        }

        // 2. Budget plausibility: opportunity minimum investment cannot exceed customer max budget
        if ($opp->min_investment > $budgetLimits['max']) {
            return ['passed' => false, 'reasons' => [], 'caveats' => ['Minimum investment exceeds allocated budget']];
        }

        $reasons[] = 'Total setup outlay ('.Opportunity::formatInr($opp->min_investment).' - '.Opportunity::formatInr($opp->max_investment).') is within your specified capital range.';

        // 3. Location / Territory availability
        if (! empty($criteria['state'])) {
            $hasState = $opp->locations->contains(function ($loc) use ($criteria) {
                return strtoupper($loc->state_code) === strtoupper($criteria['state']) && $loc->status === 'available';
            });

            if (! $hasState) {
                return ['passed' => false, 'reasons' => [], 'caveats' => ['Territory not open in selected state']];
            }

            $matchedLoc = $opp->locations->firstWhere('state_code', strtoupper($criteria['state']));
            $reasons[] = "Territory intake is active in {$matchedLoc->state_name} ({$matchedLoc->territory_tier}).";

            if ($matchedLoc->is_exclusive) {
                $caveats[] = 'Territorial exclusivity radius applies; location pin requires franchisor approval.';
            }
        }

        return [
            'passed' => true,
            'reasons' => $reasons,
            'caveats' => $caveats,
        ];
    }

    /**
     * Calculate explainable fit score (0 - 100) based on weighted factors.
     * Weights:
     * - Budget fit: 30 pts
     * - Space & Infrastructure fit: 20 pts
     * - Location fit: 20 pts
     * - Sector preference fit: 15 pts
     * - Experience compatibility: 15 pts
     */
    protected function calculateFitScore(Opportunity $opp, array $criteria, array $budgetLimits): array
    {
        $totalScore = 0;
        $breakdown = [];
        $reasons = [];
        $caveats = [];

        // 1. Budget Fit (Max 30 pts)
        // If max opportunity investment is comfortably under max customer budget -> 30 pts
        if ($opp->max_investment <= $budgetLimits['max']) {
            $budgetPts = 30;
            $reasons[] = 'Both minimum and maximum setup estimates fit comfortably inside your capital capacity.';
        } else {
            $budgetPts = 20;
            $caveats[] = 'Upper CapEx estimate ('.Opportunity::formatInr($opp->max_investment).') may require additional working capital buffer.';
        }
        $totalScore += $budgetPts;
        $breakdown['budget'] = $budgetPts;

        // 2. Space & Infrastructure Fit (Max 20 pts)
        $spaceSqft = ! empty($criteria['carpet_area_sqft']) ? (int) $criteria['carpet_area_sqft'] : null;
        if ($spaceSqft && $opp->min_space_sqft) {
            if ($spaceSqft >= $opp->min_space_sqft) {
                $spacePts = 20;
                $reasons[] = "Your available commercial space ({$spaceSqft} sq ft) satisfies the brand requirement ({$opp->min_space_sqft} sq ft).";
            } else {
                $spacePts = 5;
                $caveats[] = "Brand requires minimum {$opp->min_space_sqft} sq ft; your space ({$spaceSqft} sq ft) may require kiosk format adaptation.";
            }
        } else {
            $spacePts = 15; // Neutral baseline when space is flexible or not yet specified
        }
        $totalScore += $spacePts;
        $breakdown['space'] = $spacePts;

        // 3. Location & Territory Tier (Max 20 pts)
        $locPts = 20;
        if (! empty($criteria['district'])) {
            $hasDistrict = $opp->locations->contains(function ($loc) use ($criteria) {
                return strcasecmp($loc->district, $criteria['district']) === 0;
            });
            if ($hasDistrict) {
                $reasons[] = "Direct city allocation available in {$criteria['district']}.";
            }
        }
        $totalScore += $locPts;
        $breakdown['location'] = $locPts;

        // 4. Sector Preference Fit (Max 15 pts)
        if (! empty($criteria['sector'])) {
            if ($opp->category->slug === $criteria['sector']) {
                $sectorPts = 15;
                $reasons[] = "Directly matches your indicated sector interest: {$opp->category->name}.";
            } else {
                $sectorPts = 5;
            }
        } else {
            $sectorPts = 15; // All sectors considered equally if no preference specified
        }
        $totalScore += $sectorPts;
        $breakdown['sector'] = $sectorPts;

        // 5. Experience Compatibility (Max 15 pts)
        $exp = $criteria['experience'] ?? 'first_time';
        $mandates = $opp->requirements->where('requirement_type', 'experience');
        if ($mandates->isEmpty() || $exp !== 'first_time') {
            $expPts = 15;
            $reasons[] = 'Operating model includes comprehensive brand training; prior sector background not mandatory.';
        } else {
            $expPts = 10;
            $caveats[] = 'Full-time owner operator engagement strongly recommended by franchisor.';
        }
        $totalScore += $expPts;
        $breakdown['experience'] = $expPts;

        return [
            'total_score' => min(100, $totalScore),
            'breakdown' => $breakdown,
            'reasons' => $reasons,
            'caveats' => $caveats,
        ];
    }
}
