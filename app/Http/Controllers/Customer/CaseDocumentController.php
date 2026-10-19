<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentConsent;
use App\Models\Enquiry;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CaseDocumentController extends Controller
{
    protected DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Customer view for case documents and pending/granted consent requests.
     */
    public function show(Request $request, Enquiry $enquiry): View
    {
        // Enforce ownership: customer can only view their own case documents
        if ($enquiry->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to this case repository.');
        }

        $enquiry->load([
            'assignedCaseManager',
            'opportunity.company',
            'documents.uploader',
            'documents.verifier',
            'consents.opportunity.company',
            'consents.requester',
        ]);

        return view('customer.cases.documents', [
            'enquiry' => $enquiry,
            'documents' => $enquiry->documents,
            'consents' => $enquiry->consents,
        ]);
    }

    /**
     * Customer uploads a required document.
     */
    public function store(Request $request, Enquiry $enquiry): RedirectResponse
    {
        if ($enquiry->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:10240'],
            'category' => ['required', 'string', 'in:identity,financial,property,other'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'document.mimes' => 'Only PDF, PNG, and JPEG documents are permitted for security.',
            'document.max' => 'Document size must be under 10 MB.',
        ]);

        $this->documentService->uploadDocument(
            $enquiry,
            $request->file('document'),
            $validated,
            $request->user()
        );

        return back()->with('status', 'Your document has been securely uploaded to your case vault.');
    }

    /**
     * Customer downloads their own document.
     */
    public function download(Request $request, Document $document): BinaryFileResponse
    {
        return $this->documentService->downloadDocument($document, $request->user(), $request);
    }

    /**
     * Customer explicitly grants consent to share documents.
     */
    public function grantConsent(Request $request, DocumentConsent $consent): RedirectResponse
    {
        $this->documentService->grantConsent($consent, $request->user(), $request);

        return back()->with('status', 'Document sharing authorization granted. Your case specialist will now coordinate with the brand.');
    }

    /**
     * Customer revokes consent.
     */
    public function revokeConsent(Request $request, DocumentConsent $consent): RedirectResponse
    {
        $this->documentService->revokeConsent($consent, $request->user(), $request);

        return back()->with('status', 'Document sharing authorization has been revoked.');
    }
}
