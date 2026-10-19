<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DocumentConsent;
use App\Models\Grievance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataSubjectController extends Controller
{
    /**
     * Download machine-readable personal data archive (DPDPA 2023 compliance).
     */
    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();

        $enquiries = $user->enquiries()
            ->with([
                'opportunity.company',
                'documents',
                'consents.opportunity.company',
            ])
            ->get();

        $grievances = Grievance::where('email', $user->email)->get();

        $exportData = [
            'data_subject_export_metadata' => [
                'regulatory_framework' => 'Digital Personal Data Protection Act (DPDPA) 2023',
                'export_id' => (string) Str::uuid(),
                'generated_at' => now()->toIso8601String(),
                'platform' => 'Franchise Dekho — Managed Business Opportunity Platform',
            ],
            'principal_profile' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'city' => $user->city,
                'state' => $user->state,
                'role' => $user->role,
                'registered_at' => $user->created_at?->toIso8601String(),
                'last_login_at' => $user->last_login_at?->toIso8601String(),
            ],
            'commercial_enquiries' => $enquiries->map(function ($enquiry) {
                return [
                    'reference_code' => $enquiry->reference_code,
                    'opportunity_title' => $enquiry->opportunity?->title,
                    'company_name' => $enquiry->opportunity?->company?->legal_name,
                    'target_state' => $enquiry->target_state,
                    'target_city' => $enquiry->target_city,
                    'budget_range' => $enquiry->budget_range,
                    'carpet_area_sqft' => $enquiry->carpet_area_sqft,
                    'preferred_timeline' => $enquiry->preferred_timeline,
                    'stage' => $enquiry->stage,
                    'status' => $enquiry->status,
                    'consent_given' => $enquiry->consent_given,
                    'consent_recorded_at' => $enquiry->consent_recorded_at?->toIso8601String(),
                    'created_at' => $enquiry->created_at?->toIso8601String(),
                ];
            })->all(),
            'vault_documents_inventory' => $enquiries->flatMap(function ($enquiry) {
                return $enquiry->documents->map(function ($doc) use ($enquiry) {
                    return [
                        'case_reference' => $enquiry->reference_code,
                        'title' => $doc->title,
                        'category' => $doc->category,
                        'mime_type' => $doc->mime_type,
                        'file_size_bytes' => $doc->file_size_bytes,
                        'verification_status' => $doc->verification_status,
                        'uploaded_at' => $doc->created_at?->toIso8601String(),
                    ];
                });
            })->values()->all(),
            'third_party_consents' => $enquiries->flatMap(function ($enquiry) {
                return $enquiry->consents->map(function ($c) use ($enquiry) {
                    return [
                        'case_reference' => $enquiry->reference_code,
                        'recipient_entity' => $c->opportunity?->company?->legal_name ?? 'Brand Franchisor',
                        'scope_summary' => $c->scope_summary,
                        'status' => $c->status,
                        'granted_at' => $c->granted_at?->toIso8601String(),
                        'revoked_at' => $c->revoked_at?->toIso8601String(),
                    ];
                });
            })->values()->all(),
            'statutory_grievances' => $grievances->map(function ($g) {
                return [
                    'tracking_code' => $g->tracking_code,
                    'category' => $g->category,
                    'subject' => $g->subject,
                    'status' => $g->status,
                    'priority' => $g->priority,
                    'created_at' => $g->created_at?->toIso8601String(),
                    'resolved_at' => $g->resolved_at?->toIso8601String(),
                ];
            })->all(),
        ];

        $filename = 'franchisedekho-data-export-principal-'.$user->id.'-'.date('YmdHis').'.json';

        return response()->streamDownload(function () use ($exportData) {
            echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Show account erasure and personal data deletion request form.
     */
    public function showDeletion(Request $request): View
    {
        $user = $request->user();
        $enquiriesCount = $user->enquiries()->count();

        return view('customer.data-deletion', [
            'user' => $user,
            'enquiriesCount' => $enquiriesCount,
        ]);
    }

    /**
     * Process permanent account deletion and identity anonymization.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'confirmation' => ['accepted'],
        ], [
            'confirmation.accepted' => 'You must check the confirmation box acknowledging that account deletion and data anonymization are permanent.',
        ]);

        // Revoke any active document sharing consents
        foreach ($user->enquiries as $enquiry) {
            $enquiry->consents()
                ->where('status', DocumentConsent::STATUS_GRANTED)
                ->update([
                    'status' => DocumentConsent::STATUS_REVOKED,
                    'revoked_at' => now(),
                ]);

            // Anonymize enquiry personal info while maintaining non-identifiable commercial record
            $enquiry->update([
                'full_name' => 'Anonymized Data Principal',
                'phone' => '+910000000000',
                'email' => 'erased-'.substr(hash('sha256', $user->email), 0, 12).'@anonymized.franchisedekho.local',
                'notes' => '[REDACTED PURSUANT TO DATA PRINCIPAL ERASURE REQUEST]',
                'ip_address' => null,
            ]);
        }

        // Anonymize user account
        $userId = $user->id;
        $user->forceFill([
            'name' => 'Anonymized User #'.$userId,
            'email' => 'erased-user-'.$userId.'-'.Str::random(8).'@anonymized.franchisedekho.local',
            'phone' => null,
            'city' => null,
            'state' => null,
            'is_active' => false,
            'password' => Hash::make(Str::random(40)),
            'remember_token' => null,
        ])->save();

        // Logout customer session
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Your account and personal identifiers have been permanently erased and anonymized in compliance with the Digital Personal Data Protection Act (DPDPA).');
    }
}
