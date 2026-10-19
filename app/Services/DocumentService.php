<?php

namespace App\Services;

use App\Models\CaseCommunication;
use App\Models\Document;
use App\Models\DocumentAccessLog;
use App\Models\DocumentConsent;
use App\Models\Enquiry;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentService
{
    public const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'image/jpg',
    ];

    public const MAX_FILE_SIZE_BYTES = 10485760; // 10 MB

    /**
     * Upload and store a confidential case document into private storage.
     */
    public function uploadDocument(Enquiry $enquiry, UploadedFile $file, array $attributes, User $uploader): Document
    {
        $mime = $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            throw new InvalidArgumentException("File type [{$mime}] is not permitted. Only PDF, PNG, and JPEG files are allowed.");
        }

        if ($file->getSize() > self::MAX_FILE_SIZE_BYTES) {
            throw new InvalidArgumentException('File size exceeds the 10 MB maximum limit.');
        }

        $extension = $file->guessExtension() ?: 'bin';
        $safeStorageName = Str::uuid()->toString() . '.' . $extension;
        $storageDirectory = "cases/{$enquiry->id}";

        // Store file strictly on private local disk
        $storagePath = $file->storeAs($storageDirectory, $safeStorageName, 'local');

        $document = Document::create([
            'enquiry_id' => $enquiry->id,
            'uploaded_by_user_id' => $uploader->id,
            'category' => $attributes['category'] ?? Document::CATEGORY_OTHER,
            'title' => $attributes['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'original_filename' => $file->getClientOriginalName(),
            'storage_path' => $storagePath,
            'disk' => 'local',
            'mime_type' => $mime,
            'file_size_bytes' => $file->getSize(),
            'verification_status' => Document::STATUS_PENDING,
            'notes' => $attributes['notes'] ?? null,
        ]);

        // Record audit upload entry
        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => $uploader->id,
            'action' => 'upload',
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);

        return $document;
    }

    /**
     * Download a private document with strict authorization check and access logging.
     */
    public function downloadDocument(Document $document, User $user, Request $request): BinaryFileResponse
    {
        $enquiry = $document->enquiry;

        // Authorization check: User must be staff OR the customer who owns the case
        $isOwnerCustomer = ($enquiry->user_id === $user->id);
        $isStaff = $user->isStaff();

        if (! $isOwnerCustomer && ! $isStaff) {
            throw new AuthorizationException('You do not have permission to access or download this document.');
        }

        if (! $document->existsInStorage()) {
            abort(404, 'The requested document file could not be found in storage.');
        }

        // Log access audit trail
        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'action' => 'download',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $absolutePath = $document->absoluteStoragePath();

        return response()->download($absolutePath, $document->original_filename, [
            'Content-Type' => $document->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Mark document verification outcome (verified / rejected).
     */
    public function verifyDocument(Document $document, string $status, User $verifier, ?string $notes = null): Document
    {
        if (! in_array($status, [Document::STATUS_VERIFIED, Document::STATUS_REJECTED], true)) {
            throw new InvalidArgumentException("Invalid verification status [{$status}].");
        }

        $document->update([
            'verification_status' => $status,
            'verified_by_user_id' => $verifier->id,
            'verified_at' => now(),
            'notes' => $notes ?? $document->notes,
        ]);

        return $document->fresh(['verifier']);
    }

    /**
     * Staff requests customer consent before sharing specific documents with a brand.
     */
    public function requestConsent(
        Enquiry $enquiry,
        ?Opportunity $opportunity,
        array $documentIds,
        string $scopeSummary,
        User $staff
    ): DocumentConsent {
        // Validate all document IDs belong to this enquiry
        $validCount = $enquiry->documents()->whereIn('id', $documentIds)->count();
        if ($validCount !== count($documentIds)) {
            throw new InvalidArgumentException('One or more selected documents do not belong to this case.');
        }

        $customer = $enquiry->user;
        if (! $customer) {
            throw new InvalidArgumentException('This case is not linked to a registered customer account.');
        }

        $consent = DocumentConsent::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => $customer->id,
            'opportunity_id' => $opportunity?->id ?? $enquiry->opportunity_id,
            'requested_by_user_id' => $staff->id,
            'scope_summary' => $scopeSummary,
            'included_document_ids' => $documentIds,
            'status' => DocumentConsent::STATUS_PENDING,
        ]);

        // Publish customer-visible milestone update to inform applicant
        $brandName = $opportunity?->company?->brand_name ?? $enquiry->opportunity?->company?->brand_name ?? 'Target Brand';
        CaseCommunication::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => $staff->id,
            'type' => CaseCommunication::TYPE_CUSTOMER_UPDATE,
            'is_customer_visible' => true,
            'subject' => "Consent Authorization Requested for {$brandName}",
            'message' => "Your case specialist has compiled {$validCount} verified document(s) for submission to {$brandName}. Please visit your Case Documents tab to review the disclosure pack and authorize sharing.",
        ]);

        return $consent;
    }

    /**
     * Customer explicitly grants consent to share documents with brand.
     */
    public function grantConsent(DocumentConsent $consent, User $customer, Request $request): DocumentConsent
    {
        if ($consent->user_id !== $customer->id) {
            throw new AuthorizationException('You are not authorized to grant consent for this request.');
        }

        $consent->grant($customer, $request);

        $brandName = $consent->opportunity?->company?->brand_name ?? 'Target Franchisor';
        CaseCommunication::create([
            'enquiry_id' => $consent->enquiry_id,
            'user_id' => $customer->id,
            'type' => CaseCommunication::TYPE_CUSTOMER_UPDATE,
            'is_customer_visible' => true,
            'subject' => "Document Sharing Authorized for {$brandName}",
            'message' => "Customer explicitly granted authorization on {$consent->granted_at->format('d M Y, H:i')} to share approved disclosure pack with {$brandName}.",
        ]);

        return $consent->fresh();
    }

    /**
     * Customer revokes previously granted document sharing consent.
     */
    public function revokeConsent(DocumentConsent $consent, User $customer, Request $request): DocumentConsent
    {
        if ($consent->user_id !== $customer->id) {
            throw new AuthorizationException('You are not authorized to revoke consent for this request.');
        }

        $consent->revoke($customer, $request);

        $brandName = $consent->opportunity?->company?->brand_name ?? 'Target Franchisor';
        CaseCommunication::create([
            'enquiry_id' => $consent->enquiry_id,
            'user_id' => $customer->id,
            'type' => CaseCommunication::TYPE_CUSTOMER_UPDATE,
            'is_customer_visible' => true,
            'subject' => "Document Sharing Revoked for {$brandName}",
            'message' => "Customer revoked authorization to share documents with {$brandName}.",
        ]);

        return $consent->fresh();
    }
}
