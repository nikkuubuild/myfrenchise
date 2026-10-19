<?php

namespace App\Console\Commands;

use App\Models\Enquiry;
use App\Models\Grievance;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ScrubExpiredPrivacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'privacy:scrub-expired {--days=180 : Days after which guest applications without customer accounts are scrubbed} {--force : Run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrub and anonymize expired guest inquiries and statutory retention records in compliance with DPDPA 2023';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Scanning for guest records created prior to {$cutoffDate->toDateString()} ({$days} days retention threshold)...");

        // Find guest inquiries (not associated with an active registered user) older than threshold
        $guestEnquiries = Enquiry::whereNull('user_id')
            ->where('created_at', '<', $cutoffDate)
            ->where('full_name', '!=', 'Anonymized Data Principal');

        $count = $guestEnquiries->count();

        if ($count === 0) {
            $this->info('No expired guest records found matching the retention criteria.');
        } else {
            $updated = $guestEnquiries->update([
                'full_name' => 'Anonymized Guest Record',
                'phone' => '+910000000000',
                'email' => 'scrubbed-guest@anonymized.franchisedekho.local',
                'notes' => '[REDACTED PURSUANT TO STATUTORY DATA RETENTION POLICY]',
                'ip_address' => null,
            ]);

            $this->info("Successfully scrubbed and anonymized {$updated} expired guest enquiry records.");
        }

        // Anonymize IP addresses on resolved grievances older than 90 days to minimize data footprint
        $resolvedGrievanceCutoff = Carbon::now()->subDays(90);
        $resolvedGrievances = Grievance::where('status', Grievance::STATUS_RESOLVED)
            ->where('resolved_at', '<', $resolvedGrievanceCutoff)
            ->whereNotNull('ip_address');

        $grievanceCount = $resolvedGrievances->count();
        if ($grievanceCount > 0) {
            $scrubbedGrievanceIps = $resolvedGrievances->update(['ip_address' => null]);
            $this->info("Scrubbed network IP addresses on {$scrubbedGrievanceIps} historical resolved grievances.");
        }

        $this->info('Privacy scrubbing routine completed successfully.');

        return self::SUCCESS;
    }
}
