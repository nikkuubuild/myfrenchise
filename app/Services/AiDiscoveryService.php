<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Opportunity;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AiDiscoveryService
{
    /**
     * Process an entrepreneur query through strict evidence grounding and safety guardrails.
     *
     * @param string $query User natural language question or discovery prompt
     * @return array Grounded response payload with answer narrative, citations, opportunities, and uncertainty disclosures.
     */
    public function ask(string $query): array
    {
        $cleanQuery = trim($query);

        // 1. Guardrail Safety Engine: check for prohibited actions or false promises
        $guardrailResult = $this->evaluateGuardrails($cleanQuery);
        if ($guardrailResult['blocked']) {
            return [
                'query' => $cleanQuery,
                'status' => 'guardrail_intervention',
                'answer' => $guardrailResult['message'],
                'matched_opportunities' => collect(),
                'articles' => collect(),
                'statutory_sources' => $guardrailResult['sources'] ?? [],
                'uncertainty_notes' => [],
                'guardrail_flags' => [$guardrailResult['flag']],
                'verified_at' => now()->toIso8601String(),
                'disclaimer' => $this->getMandatoryDisclaimer(),
            ];
        }

        // 2. Entity and Intent Extraction
        $extracted = $this->extractQueryEntities($cleanQuery);

        // 3. Evidence Grounding: Retrieve exclusively published and verified records
        $opportunities = $this->retrieveMatchingOpportunities($extracted);
        $articles = $this->retrieveRelevantArticles($cleanQuery, $extracted);

        // 4. Synthesize Grounded Response
        $synthesis = $this->synthesizeResponse($cleanQuery, $extracted, $opportunities, $articles);

        return [
            'query' => $cleanQuery,
            'status' => 'grounded_success',
            'answer' => $synthesis['narrative'],
            'matched_opportunities' => $opportunities,
            'articles' => $articles,
            'statutory_sources' => $synthesis['statutory_sources'],
            'uncertainty_notes' => $synthesis['uncertainty_notes'],
            'guardrail_flags' => [],
            'verified_at' => now()->toIso8601String(),
            'disclaimer' => $this->getMandatoryDisclaimer(),
        ];
    }

    /**
     * Enforce strict non-negotiable safety boundaries.
     */
    protected function evaluateGuardrails(string $query): array
    {
        $lower = strtolower($query);

        // Policy Guardrail 1: Payment and financial transactions prohibited via AI
        if (Str::contains($lower, ['pay now', 'charge my', 'take my payment', 'transfer money', 'accept payment', 'authorize payment', 'upi pin', 'card details'])) {
            return [
                'blocked' => true,
                'flag' => 'PAYMENT_AUTHORIZATION_PROHIBITED',
                'message' => "Payment Processing Notice: Franchise Dekho's AI discovery engine is strictly prohibited from accepting payments, charging cards, or authorizing financial transactions. All managed service packages and legal verification fees are processed exclusively through our statutory, GST-compliant checkout portal with human case manager assignment.",
                'sources' => ['Integrated Goods and Services Tax Act 2017', 'Payment and Settlement Systems Act 2007'],
            ];
        }

        // Policy Guardrail 2: Automatic application or franchise approval prohibited
        if (Str::contains($lower, ['approve me', 'approve my application', 'grant me franchise', 'issue my license', 'give me approval', 'approve listing'])) {
            return [
                'blocked' => true,
                'flag' => 'AUTOMATIC_APPROVAL_PROHIBITED',
                'message' => "Application Authority Notice: Artificial intelligence cannot evaluate or grant commercial franchise approvals. Franchise Dekho coordinates verified documentation with the respective brand's corporate leadership. The parent brand retains sole, non-delegable authority over franchisee selection, territory allocation, and contract execution.",
                'sources' => ['Indian Contract Act 1872', 'Franchise Dekho Governance Standard'],
            ];
        }

        // Policy Guardrail 3: Guaranteed returns, turnover, or profit promises prohibited
        if (Str::contains($lower, ['guarantee profit', 'guaranteed return', 'guaranteed roi', 'assured return', 'zero risk', '100% profit', 'guaranteed income', 'guaranteed turnover'])) {
            return [
                'blocked' => true,
                'flag' => 'EARNINGS_PROMISES_PROHIBITED',
                'message' => "Non-Negotiable Risk & Returns Notice: Neither Franchise Dekho nor any legitimate franchisor can guarantee business revenue, profit margins, or financial returns. Every commercial enterprise carries market, location, competitive, and operational risks. Financial statements must be reviewed independently with a qualified Chartered Accountant.",
                'sources' => ['Consumer Protection Act 2019 (Misleading Advertisements)', 'Companies Act 2013'],
            ];
        }

        return ['blocked' => false];
    }

    /**
     * Extract structured parameters (budget, state, sector, space) from natural language.
     */
    protected function extractQueryEntities(string $query): array
    {
        $lower = strtolower($query);
        $entities = [
            'budget_max' => null,
            'budget_min' => null,
            'state_code' => null,
            'sector_slug' => null,
            'space_sqft' => null,
            'is_regulatory_query' => false,
        ];

        // Budget extraction
        if (preg_match('/(?:under|below|less than|up to)\s*(?:₹|rs\.?|inr)?\s*(\d+(?:\.\d+)?)\s*(lakh|lac|l|crore|cr)?/i', $query, $matches)) {
            $num = (float) $matches[1];
            $unit = strtolower($matches[2] ?? 'lakh');
            $multiplier = in_array($unit, ['crore', 'cr']) ? 10000000 : 100000;
            $entities['budget_max'] = (int) ($num * $multiplier);
        } elseif (preg_match('/(\d+(?:\.\d+)?)\s*(?:-|to)\s*(\d+(?:\.\d+)?)\s*(lakh|lac|l)?/i', $query, $matches)) {
            $minNum = (float) $matches[1];
            $maxNum = (float) $matches[2];
            $entities['budget_min'] = (int) ($minNum * 100000);
            $entities['budget_max'] = (int) ($maxNum * 100000);
        } elseif (Str::contains($lower, ['under 5', 'under 5l', 'under 5 lakh', 'under 5 lac'])) {
            $entities['budget_max'] = 500000;
        } elseif (Str::contains($lower, ['10 lakh', '10l', '10 lac', 'under 10'])) {
            $entities['budget_max'] = 1000000;
        } elseif (Str::contains($lower, ['15 lakh', '15l', '15 lac', 'under 15'])) {
            $entities['budget_max'] = 1500000;
        } elseif (Str::contains($lower, ['30 lakh', '30l', '30 lac', 'under 30'])) {
            $entities['budget_max'] = 3000000;
        }

        // State extraction
        $states = [
            'UP' => ['uttar pradesh', 'up', 'lucknow', 'kanpur', 'noida', 'varanasi', 'agra'],
            'DL' => ['delhi', 'ncr', 'new delhi', 'dl'],
            'MH' => ['maharashtra', 'mumbai', 'pune', 'nagpur', 'nashik', 'mh'],
            'HR' => ['haryana', 'gurgaon', 'gurugram', 'faridabad', 'hr'],
            'RJ' => ['rajasthan', 'jaipur', 'jodhpur', 'udaipur', 'rj'],
            'KA' => ['karnataka', 'bangalore', 'bengaluru', 'mysore', 'ka'],
            'GJ' => ['gujarat', 'ahmedabad', 'surat', 'vadodara', 'gj'],
            'TN' => ['tamil nadu', 'chennai', 'coimbatore', 'tn'],
            'WB' => ['west bengal', 'kolkata', 'wb'],
        ];

        foreach ($states as $code => $keywords) {
            foreach ($keywords as $kw) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $query)) {
                    $entities['state_code'] = $code;
                    break 2;
                }
            }
        }

        // Sector extraction
        if (Str::contains($lower, ['food', 'beverage', 'chai', 'tea', 'cafe', 'restaurant', 'qsr', 'bakery', 'kiosk'])) {
            $entities['sector_slug'] = 'food-beverage';
        } elseif (Str::contains($lower, ['courier', 'logistics', 'parcel', 'delivery', 'cargo'])) {
            $entities['sector_slug'] = 'logistics';
        } elseif (Str::contains($lower, ['health', 'clinic', 'diagnostic', 'pathology', 'pharmacy', 'medical'])) {
            $entities['sector_slug'] = 'healthcare';
        } elseif (Str::contains($lower, ['retail', 'mart', 'grocery', 'supermarket', 'fmcg', 'distribution'])) {
            $entities['sector_slug'] = 'retail-fmcg';
        } elseif (Str::contains($lower, ['education', 'school', 'preschool', 'coaching', 'training', 'institute'])) {
            $entities['sector_slug'] = 'education';
        }

        // Space extraction
        if (preg_match('/(\d+)\s*(?:sq\.?\s*ft|sqft|square feet)/i', $query, $matches)) {
            $entities['space_sqft'] = (int) $matches[1];
        }

        // Regulatory / Statutory intent
        if (Str::contains($lower, ['gst', 'sac', 'fssai', 'trademark', 'agreement', 'royalty', 'fee', 'due diligence', 'lease', 'license', 'legal', 'law'])) {
            $entities['is_regulatory_query'] = true;
        }

        return $entities;
    }

    /**
     * Retrieve matching opportunities grounded exclusively in published and verified records.
     */
    protected function retrieveMatchingOpportunities(array $entities): Collection
    {
        $query = Opportunity::published()
            ->with(['company', 'category', 'financialBreakdowns', 'locations', 'requirements']);

        if (!empty($entities['sector_slug'])) {
            $query->whereHas('category', function ($q) use ($entities) {
                $q->where('slug', $entities['sector_slug']);
            });
        }

        if (!empty($entities['budget_max'])) {
            $query->where('min_investment', '<=', $entities['budget_max']);
        }

        if (!empty($entities['budget_min'])) {
            $query->where('max_investment', '>=', $entities['budget_min']);
        }

        if (!empty($entities['state_code'])) {
            $query->whereHas('locations', function ($q) use ($entities) {
                $q->where('state_code', $entities['state_code']);
            });
        }

        if (!empty($entities['space_sqft'])) {
            $query->where(function ($q) use ($entities) {
                $q->whereNull('min_space_sqft')
                    ->orWhere('min_space_sqft', '<=', $entities['space_sqft']);
            });
        }

        return $query->orderBy('last_verified_at', 'desc')->take(4)->get();
    }

    /**
     * Retrieve relevant educational due diligence articles.
     */
    protected function retrieveRelevantArticles(string $query, array $entities): Collection
    {
        $articleQuery = Article::published()->with('category');

        if (!empty($entities['sector_slug'])) {
            $articleQuery->where(function ($q) use ($entities) {
                $q->whereHas('category', fn ($cq) => $cq->where('slug', $entities['sector_slug']))
                    ->orWhereNull('category_id');
            });
        }

        return $articleQuery->recent()->take(2)->get();
    }

    /**
     * Synthesize grounded narrative response citing verified records, dates, and uncertainty.
     */
    protected function synthesizeResponse(string $query, array $entities, Collection $opportunities, Collection $articles): array
    {
        $narrative = '';
        $statutorySources = collect();
        $uncertaintyNotes = collect();

        // Scenario A: Relevant verified opportunities found
        if ($opportunities->isNotEmpty()) {
            $count = $opportunities->count();
            $sectorName = $entities['sector_slug'] ? ucwords(str_replace('-', ' ', $entities['sector_slug'])) : 'all audited sectors';

            $narrative .= "Based on Franchise Dekho's verified database, we identified {$count} approved business opportunity record(s) matching your criteria";
            if (!empty($entities['budget_max'])) {
                $formattedMax = Opportunity::formatInr($entities['budget_max']);
                $narrative .= " within {$formattedMax} capital outlay";
            }
            if (!empty($entities['state_code'])) {
                $narrative .= " in state ({$entities['state_code']})";
            }
            $narrative .= ":\n\n";

            foreach ($opportunities as $opp) {
                $minInr = Opportunity::formatInr($opp->min_investment);
                $maxInr = Opportunity::formatInr($opp->max_investment);
                $verifiedDate = $opp->last_verified_at ? $opp->last_verified_at->format('d M Y') : 'Recent Audit';
                $cinText = $opp->company->cin ? " (CIN: {$opp->company->cin})" : '';

                $narrative .= "• **{$opp->title}** (Operated by {$opp->company->legal_name}{$cinText})\n";
                $narrative .= "  - **Verified Outlay:** {$minInr} to {$maxInr} ({$opp->business_model} Model)\n";
                $narrative .= "  - **Audit Provenance:** Independent regulatory check verified on {$verifiedDate}\n";
                $narrative .= "  - **Infrastructure Requirement:** Minimum {$opp->min_space_sqft} Sq. Ft. commercial carpet area\n\n";

                // Add statutory acts cited
                $statutorySources->push('Ministry of Corporate Affairs (MCA21)');
                $statutorySources->push('Controller General of Patents, Designs and Trade Marks (IP India)');
            }

            // Acknowledge uncertainty & local due diligence requirements
            $uncertaintyNotes->push('Site-Specific Power & Water: Three-phase commercial power availability must be verified at the target landlord property.');
            $uncertaintyNotes->push('Municipal Zoning: Shop & Establishment registration and local municipal commercial conformity must be confirmed before lease signing.');
            $uncertaintyNotes->push('Brand Exclusivity: Radial territory locks require written franchisor confirmation for your exact commercial pin code.');

        } else {
            // Scenario B: No approved records meet criteria (Transparent acknowledgement of negative result)
            $narrative .= "Search Transparency Notice: We currently have zero published opportunities matching your specified parameters that have passed Franchise Dekho's mandatory 5-point statutory audit.\n\n";
            $narrative .= "We deliberately do not display unverified broker leads, speculative listings, or marketing directories without Ministry of Corporate Affairs and Trademark verification.\n\n";
            $narrative .= "Recommended Next Steps:\n";
            $narrative .= "1. Consider adjusting your investment range or exploring adjacent districts/states.\n";
            $narrative .= "2. Request an assisted needs assessment, and our human research desk will evaluate emerging franchisors in this sector.\n";

            $uncertaintyNotes->push('No verified listing currently exists with active audit credentials matching these exact constraints.');
        }

        // Append statutory/legal context if regulatory intent detected or articles present
        if ($articles->isNotEmpty() || $entities['is_regulatory_query']) {
            $narrative .= "\n**Statutory & Regulatory Due Diligence Guidelines:**\n";
            foreach ($articles as $art) {
                $narrative .= "• [Guide] *{$art->title}*: {$art->excerpt} (Re-verified: {$art->last_verified_at?->format('M Y')})\n";
                if ($art->sources_and_evidence) {
                    $statutorySources->push($art->sources_and_evidence);
                }
            }
            $statutorySources->push('Indian Contract Act 1872');
            $statutorySources->push('GST Act 2017 (SAC 998311)');
        }

        return [
            'narrative' => $narrative,
            'statutory_sources' => $statutorySources->unique()->values()->all(),
            'uncertainty_notes' => $uncertaintyNotes->values()->all(),
        ];
    }

    /**
     * Mandatory non-negotiable legal and regulatory notice.
     */
    public function getMandatoryDisclaimer(): string
    {
        return "AI Discovery & Regulatory Notice: Responses are synthesized strictly from Franchise Dekho's approved, verified company and opportunity records. Franchise Dekho AI does not guarantee financial returns, loan approvals, or commercial turnover. The parent franchisor retains sole authority over franchisee selection. Applications require human case manager coordination and customer consent.";
    }
}
