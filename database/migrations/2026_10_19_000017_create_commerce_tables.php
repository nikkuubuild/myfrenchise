<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Service Packages (Catalogue)
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('target_audience', 20)->default('customer'); // customer, company, both
            $table->text('summary');
            $table->text('description')->nullable();
            $table->json('inclusions'); // array of itemized deliverables
            $table->json('exclusions'); // array of non-deliverables (e.g. no guarantee of franchisor approval)
            $table->text('cancellation_policy'); // clear refund/cancellation disclosures
            $table->unsignedBigInteger('base_price_paise'); // e.g. 499900 = INR 4,999.00
            $table->decimal('tax_rate_percentage', 5, 2)->default(18.00); // 18% GST standard
            $table->string('sac_code', 20)->default('998311'); // Management Consulting & Verification Services
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Orders Table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique(); // e.g. FD-ORD-202610-0001
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_package_id')->constrained('service_packages');
            $table->foreignId('enquiry_id')->nullable()->constrained('enquiries')->nullOnDelete();
            $table->string('currency', 10)->default('INR');
            $table->unsignedBigInteger('subtotal_paise');
            $table->unsignedBigInteger('tax_amount_paise');
            $table->unsignedBigInteger('total_amount_paise');
            $table->string('status', 30)->default('pending'); // pending, paid, failed, cancelled, refunded
            $table->string('billing_name');
            $table->string('billing_email');
            $table->string('billing_phone', 20);
            $table->string('billing_state', 10); // e.g. MH, DL, HR, KA
            $table->string('billing_city', 100)->nullable();
            $table->string('billing_gstin', 20)->nullable(); // Optional B2B Tax GSTIN
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // 3. Payment Transactions Table
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference', 40)->unique(); // e.g. FD-TXN-202610-ABCD
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('gateway', 30)->default('razorpay'); // razorpay, cashfree, mock_gateway
            $table->string('gateway_order_id')->nullable();
            $table->string('gateway_payment_id')->nullable()->index();
            $table->string('gateway_signature')->nullable();
            $table->unsignedBigInteger('amount_paise');
            $table->string('currency', 10)->default('INR');
            $table->string('payment_method', 30)->nullable(); // upi, netbanking, card - NO raw cards/CVV/PIN
            $table->string('status', 30)->default('initiated'); // initiated, captured, failed, refunded
            $table->string('failure_reason')->nullable();
            $table->json('metadata')->nullable(); // sanitized non-sensitive metadata
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });

        // 4. Invoices Table (Statutory GST Compliance)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 40)->unique(); // e.g. FD-INV-202610-0001
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->date('invoice_date');
            $table->string('financial_year', 15)->default('2026-2027');
            $table->unsignedBigInteger('subtotal_paise');
            $table->unsignedBigInteger('cgst_paise')->default(0); // 9% for intra-state
            $table->unsignedBigInteger('sgst_paise')->default(0); // 9% for intra-state
            $table->unsignedBigInteger('igst_paise')->default(0); // 18% for inter-state
            $table->unsignedBigInteger('total_paise');
            $table->string('customer_state', 10);
            $table->string('seller_state', 10)->default('HR'); // Haryana (Gurugram operations)
            $table->boolean('is_interstate')->default(true);
            $table->string('sac_code', 20)->default('998311');
            $table->timestamps();
        });

        // 5. Refunds Table
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_reference', 40)->unique(); // e.g. FD-REF-202610-ABCD
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions')->nullOnDelete();
            $table->unsignedBigInteger('amount_paise');
            $table->text('reason');
            $table->string('status', 30)->default('requested'); // requested, processed, rejected
            $table->string('gateway_refund_id')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // 6. Webhook Events Table (Idempotency and Verification Audit)
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 30)->default('razorpay');
            $table->string('event_id', 100)->unique(); // Idempotency Key
            $table->string('event_type', 100);
            $table->json('payload');
            $table->boolean('signature_verified')->default(false);
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'event_type', 'is_processed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('service_packages');
    }
};
