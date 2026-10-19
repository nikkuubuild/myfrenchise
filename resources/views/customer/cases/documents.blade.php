<x-layout.app title="Case Documents & Sharing Consents #{{ $enquiry->reference_code }} — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('dashboard') }}" class="text-xs text-ink-muted hover:text-ink-primary hover:underline">
                            &larr; Back to My Dashboard
                        </a>
                        <span class="text-xs text-border-strong">&bull;</span>
                        <span class="font-mono text-xs font-bold text-ink-primary">{{ $enquiry->reference_code }}</span>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        Confidential Documents &amp; Sharing Authorizations
                    </h1>
                    <p class="text-xs text-ink-muted mt-1">
                        Application: <strong class="text-ink-secondary">{{ $enquiry->opportunity ? $enquiry->opportunity->company->brand_name : 'Assisted Discovery' }}</strong> &bull;
                        Assigned Specialist: <strong class="text-ink-secondary">{{ $enquiry->assignedCaseManager?->name ?? 'Assignment in Progress' }}</strong>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono px-3 py-1 bg-surface-canvas border border-border-subtle rounded-sm text-ink-muted">
                        Private Vault &bull; 10 MB Max &bull; PDF / PNG / JPEG
                    </span>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if (session('status'))
            <x-ui.alert variant="success">
                {{ session('status') }}
            </x-ui.alert>
        @endif

        @if ($errors->any())
            <x-ui.alert variant="danger" title="Please review the following:">
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <!-- PENDING CONSENT REQUESTS (CRITICAL ACTION FOR CUSTOMER) -->
        @php
            $pendingConsents = $consents->where('status', 'pending');
        @endphp

        @if($pendingConsents->count() > 0)
            <div class="space-y-4">
                <h2 class="font-serif text-xl font-bold text-status-caveat-text flex items-center gap-2">
                    <span>&#9888;</span> Action Required: Document Sharing Authorizations
                </h2>

                @foreach($pendingConsents as $consent)
                    <div class="bg-surface-canvas border-2 border-status-caveat-border rounded-sm p-6 shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-border-subtle">
                            <div>
                                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-status-caveat-text block">
                                    Pending Authorization
                                </span>
                                <h3 class="font-serif text-lg font-bold text-ink-primary">
                                    Request to Share Documents with {{ $consent->opportunity?->company?->brand_name ?? 'Target Brand' }}
                                </h3>
                            </div>
                            <span class="text-xs text-ink-muted">
                                Requested by {{ $consent->requester?->name ?? 'Case Specialist' }} on {{ $consent->created_at->format('d M Y') }}
                            </span>
                        </div>

                        <div class="bg-surface-paper/70 p-4 rounded-sm border border-border-subtle text-xs text-ink-secondary leading-relaxed">
                            <strong class="text-ink-primary block mb-1">Purpose &amp; Scope of Disclosure:</strong>
                            {{ $consent->scope_summary }}
                        </div>

                        <!-- Included Documents to be shared -->
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted block mb-2">
                                Specific Documents Included in this Disclosure Pack:
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                @foreach($consent->includedDocuments() as $doc)
                                    <div class="p-2.5 bg-surface-paper/40 border border-border-subtle rounded-sm flex items-center justify-between">
                                        <div>
                                            <span class="font-medium text-ink-primary block">{{ $doc->title }}</span>
                                            <span class="text-[10px] text-ink-muted uppercase font-mono">{{ $doc->category }} &bull; {{ $doc->formattedSize() }}</span>
                                        </div>
                                        <a href="{{ route('customer.cases.documents.download', $doc->id) }}" class="text-xs font-medium text-ink-primary hover:underline">
                                            Inspect File &darr;
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Formal Consent Form -->
                        <div class="pt-4 border-t border-border-subtle flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <p class="text-[11px] text-ink-muted max-w-xl">
                                Non-Negotiable Privacy Guarantee: Franchise Dekho will <strong>never</strong> submit your confidential documents to any brand or external party without this explicit affirmative confirmation.
                            </p>

                            <form method="POST" action="{{ route('customer.cases.consents.grant', $consent->id) }}" class="shrink-0">
                                @csrf
                                <x-ui.button type="submit" variant="primary" class="!py-2.5 !px-5 !text-xs w-full sm:w-auto">
                                    Authorize &amp; Grant Consent &rarr;
                                </x-ui.button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- PREVIOUSLY GRANTED & HISTORICAL CONSENTS -->
        @php
            $resolvedConsents = $consents->whereIn('status', ['granted', 'revoked']);
        @endphp
        @if($resolvedConsents->count() > 0)
            <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-sm space-y-4">
                <h3 class="font-serif text-base font-bold text-ink-primary pb-2 border-b border-border-subtle">
                    Document Sharing Authorization History
                </h3>
                <div class="space-y-3">
                    @foreach($resolvedConsents as $histConsent)
                        <div class="p-3 bg-surface-paper/40 border border-border-subtle rounded-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <strong class="text-ink-primary">{{ $histConsent->opportunity?->company?->brand_name ?? 'Target Brand' }}</strong>
                                    <x-ui.badge variant="{{ $histConsent->status === 'granted' ? 'verified' : 'neutral' }}" size="sm">
                                        {{ ucfirst($histConsent->status) }}
                                    </x-ui.badge>
                                </div>
                                <p class="text-ink-muted text-[11px]">{{ $histConsent->scope_summary }}</p>
                                <span class="text-[10px] text-ink-muted block mt-1 font-mono">
                                    {{ $histConsent->status === 'granted' ? 'Authorized on '.$histConsent->granted_at?->format('d M Y, H:i') : 'Revoked on '.$histConsent->revoked_at?->format('d M Y, H:i') }}
                                    &bull; {{ count($histConsent->included_document_ids ?? []) }} document(s)
                                </span>
                            </div>

                            @if($histConsent->status === 'granted')
                                <form method="POST" action="{{ route('customer.cases.consents.revoke', $histConsent->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-700 hover:underline font-medium cursor-pointer">
                                        Revoke Authorization
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- DOCUMENT VAULT & UPLOAD -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Document Inventory -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-border-subtle">
                        <h2 class="font-serif text-lg font-bold text-ink-primary">
                            Your Uploaded Case Documents
                        </h2>
                        <span class="text-xs font-mono text-ink-muted">{{ $documents->count() }} Files in Vault</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($documents as $doc)
                            <div class="p-4 bg-surface-paper/30 border border-border-subtle rounded-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-ink-primary transition-colors">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-ink-primary text-xs">{{ $doc->title }}</span>
                                        <x-ui.badge variant="{{ $doc->verification_status === 'verified' ? 'verified' : ($doc->verification_status === 'rejected' ? 'danger' : 'neutral') }}" size="sm">
                                            {{ ucfirst($doc->verification_status) }}
                                        </x-ui.badge>
                                    </div>
                                    <p class="text-[11px] text-ink-muted">
                                        Category: <strong class="capitalize text-ink-secondary">{{ $doc->category }}</strong> &bull;
                                        File: <span class="font-mono">{{ $doc->original_filename }}</span> ({{ $doc->formattedSize() }}) &bull;
                                        Uploaded: {{ $doc->created_at->format('d M Y') }}
                                    </p>
                                    @if($doc->notes)
                                        <p class="text-[11px] text-ink-secondary italic">Note: {{ $doc->notes }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('customer.cases.documents.download', $doc->id) }}" class="text-xs font-semibold text-ink-primary px-3 py-1.5 bg-surface-canvas border border-border-strong rounded-sm hover:border-ink-primary">
                                        Download File &darr;
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-ink-muted text-xs italic">
                                No documents have been uploaded to this case vault yet. Use the upload panel to attach identity, financial, or commercial property records.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Upload Document Form -->
            <div>
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-sm space-y-4 sticky top-20">
                    <h3 class="font-serif text-base font-bold text-ink-primary pb-2 border-b border-border-subtle">
                        Upload Document to Vault
                    </h3>

                    <form method="POST" action="{{ route('customer.cases.documents.store', $enquiry->id) }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                        @csrf

                        <div>
                            <label for="category" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Document Category <span class="text-status-caveat-text">*</span>
                            </label>
                            <select id="category" name="category" required class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                                <option value="identity">Identity Proof (Aadhaar / PAN / Passport)</option>
                                <option value="financial">Financial Capacity (Bank Statement / ITR / CA Net Worth)</option>
                                <option value="property">Commercial Premises (Ownership / Lease Agreement / Layout)</option>
                                <option value="other">Other Supporting Material</option>
                            </select>
                        </div>

                        <div>
                            <label for="title" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Document Title <span class="text-status-caveat-text">*</span>
                            </label>
                            <input type="text" id="title" name="title" placeholder="e.g. 6-Month Bank Statement" required
                                   class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                        </div>

                        <div>
                            <label for="document" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Choose File <span class="text-status-caveat-text">*</span>
                            </label>
                            <input type="file" id="document" name="document" accept=".pdf,.png,.jpg,.jpeg" required
                                   class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary file:mr-2 file:py-1 file:px-2 file:rounded-sm file:border-0 file:text-xs file:bg-surface-paper file:text-ink-primary hover:file:bg-border-subtle cursor-pointer">
                            <span class="text-[10px] text-ink-muted block mt-1">PDF, PNG, or JPEG format &bull; Max 10 MB</span>
                        </div>

                        <div>
                            <label for="notes" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Optional Notes
                            </label>
                            <textarea id="notes" name="notes" rows="2" placeholder="e.g. Latest HDFC current account statement"
                                      class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary"></textarea>
                        </div>

                        <x-ui.button type="submit" variant="primary" class="w-full justify-center !py-2.5">
                            Upload to Secure Vault &rarr;
                        </x-ui.button>
                    </form>

                    <div class="pt-3 border-t border-border-subtle text-[11px] text-ink-muted leading-relaxed">
                        <p><strong>Storage Security:</strong> Files are stored strictly in private encrypted server storage and are never publicly accessible over the web.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
