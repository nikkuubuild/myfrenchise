<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Enquiry;
use App\Models\Opportunity;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CaseDocumentController extends Controller
{
    protected DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Staff uploads a document to the case dossier.
     */
    public function store(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:10240'],
            'category' => ['required', 'string', 'in:identity,financial,property,brand_disclosure,other'],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->documentService->uploadDocument(
            $enquiry,
            $request->file('document'),
            $validated,
            $request->user()
        );

        return back()->with('status', 'Document uploaded securely to private case repository.');
    }

    /**
     * Staff downloads a private document.
     */
    public function download(Request $request, Document $document): BinaryFileResponse
    {
        return $this->documentService->downloadDocument($document, $request->user(), $request);
    }

    /**
     * Staff verifies or rejects a document.
     */
    public function verify(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'verification_status' => ['required', 'in:verified,rejected'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->documentService->verifyDocument(
            $document,
            $validated['verification_status'],
            $request->user(),
            $validated['notes'] ?? null
        );

        return back()->with('status', "Document status updated to {$validated['verification_status']}.");
    }

    /**
     * Staff creates a document-sharing consent request for the customer.
     */
    public function requestConsent(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'document_ids' => ['required', 'array', 'min:1'],
            'document_ids.*' => ['integer', 'exists:documents,id'],
            'scope_summary' => ['required', 'string', 'max:1000'],
            'opportunity_id' => ['nullable', 'exists:opportunities,id'],
        ]);

        $opportunity = ! empty($validated['opportunity_id'])
            ? Opportunity::find($validated['opportunity_id'])
            : $enquiry->opportunity;

        $this->documentService->requestConsent(
            $enquiry,
            $opportunity,
            $validated['document_ids'],
            $validated['scope_summary'],
            $request->user()
        );

        return back()->with('status', 'Consent request created and notified to the applicant.');
    }
}
