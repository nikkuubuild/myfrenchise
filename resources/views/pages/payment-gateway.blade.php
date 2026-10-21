<x-layout.app title="Secure Payment — Order #{{ $order->order_number }} — Franchise Dekho" robots="noindex, nofollow">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        @if (session('error'))
            <div class="mb-6">
                <x-ui.alert variant="danger">
                    {{ session('error') }}
                </x-ui.alert>
            </div>
        @endif

        <div class="bg-surface-canvas border border-border-strong rounded-sm overflow-hidden shadow-md">
            <!-- Gateway Header -->
            <div class="bg-surface-paper p-6 border-b border-border-subtle flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono font-semibold uppercase tracking-wider text-accent-green block">
                        256-Bit Encrypted Payment Session
                    </span>
                    <h1 class="font-serif text-xl font-bold text-ink-primary">
                        Franchise Dekho Payment Gateway
                    </h1>
                </div>
                <div class="text-right">
                    <span class="text-xs text-ink-muted block uppercase text-[10px]">Total Amount</span>
                    <span class="font-mono text-xl font-bold text-accent-green">{{ $order->formatted_total }}</span>
                </div>
            </div>

            <!-- Order Specs -->
            <div class="p-6 border-b border-border-subtle bg-surface-canvas space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-ink-muted">Order Number:</span>
                    <strong class="font-mono text-ink-primary">{{ $order->order_number }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-ink-muted">Service Package:</span>
                    <span class="font-medium text-ink-primary">{{ $order->servicePackage->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-ink-muted">Payer Entity:</span>
                    <span class="text-ink-primary">{{ $order->billing_name }} ({{ $order->billing_phone }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-ink-muted">GST State (POS):</span>
                    <span class="font-mono text-ink-primary">{{ $order->billing_state }}</span>
                </div>
            </div>

            <!-- Payment Methods / Simulation Interface -->
            <div class="p-6 sm:p-8 space-y-6">
                <div class="p-4 bg-surface-paper border border-border-subtle rounded-xs space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                        <strong class="text-ink-primary">Gateway Server Signature Ready</strong>
                    </div>
                    <p class="text-ink-secondary leading-relaxed">
                        In accordance with our strict security policy, payment success is cryptographically validated using HMAC SHA256 before marking the order as paid or issuing tax invoices.
                    </p>
                </div>

                <!-- Simulation Form: Valid Payment Authorization -->
                <form method="POST" action="{{ route('checkout.callback', $order) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="razorpay_payment_id" value="{{ $mockPaymentId }}">
                    <input type="hidden" name="razorpay_signature" value="{{ $validSignature }}">

                    <div class="space-y-2">
                        <label class="block font-semibold uppercase tracking-wider text-ink-muted text-xs">
                            Select Payment Channel
                        </label>
                        <div class="grid grid-cols-3 gap-3 text-xs">
                            <label class="p-3 bg-surface-paper border border-border-strong rounded-xs flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-ink-primary">
                                <input type="radio" name="payment_method" value="upi" checked class="text-ink-primary">
                                <span class="font-semibold text-ink-primary mt-1">UPI (QR / App)</span>
                                <span class="text-[10px] text-ink-muted font-mono">GPay / PhonePe</span>
                            </label>

                            <label class="p-3 bg-surface-paper border border-border-strong rounded-xs flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-ink-primary">
                                <input type="radio" name="payment_method" value="netbanking" class="text-ink-primary">
                                <span class="font-semibold text-ink-primary mt-1">NetBanking</span>
                                <span class="text-[10px] text-ink-muted font-mono">IMPS / NEFT</span>
                            </label>

                            <label class="p-3 bg-surface-paper border border-border-strong rounded-xs flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-ink-primary">
                                <input type="radio" name="payment_method" value="card" class="text-ink-primary">
                                <span class="font-semibold text-ink-primary mt-1">Debit / Credit</span>
                                <span class="text-[10px] text-ink-muted font-mono">RuPay / Visa / MC</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between">
                        <a href="{{ route('customer.billing.index') }}" class="text-xs text-ink-muted hover:text-ink-primary underline">
                            Cancel &bull; Pay Later
                        </a>

                        <button type="submit" class="px-6 py-2.5 bg-accent-green text-white text-xs font-semibold rounded-sm hover:opacity-90 transition-opacity shadow-sm cursor-pointer">
                            Authorize &amp; Complete Payment ({{ $order->formatted_total }}) &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
