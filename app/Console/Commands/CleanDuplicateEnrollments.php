<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicateEnrollments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:clean-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate pending enrollments and orphaned duplicate pending payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting LMS duplicate cleanup...');

        $cleanedEnrollments = 0;
        $cleanedPayments = 0;

        // 1. Delete pending enrollments for students who already have an active enrollment in the same course
        $activeStudents = Enrollment::where('status', 'active')->get(['id', 'course_id', 'student_email', 'student_phone']);

        foreach ($activeStudents as $active) {
            $duplicateEnrollments = Enrollment::where('course_id', $active->course_id)
                ->where('id', '!=', $active->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($active) {
                    $q->where('student_email', $active->student_email)
                      ->orWhere('student_phone', $active->student_phone);
                })
                ->get();

            foreach ($duplicateEnrollments as $dup) {
                Payment::where('enrollment_id', $dup->id)->update([
                    'enrollment_id' => $active->id,
                ]);

                $dup->delete();
                $cleanedEnrollments++;
            }

            // Cancel any orphaned pending payments for this active student
            $dupPayments = Payment::where('course_id', $active->course_id)
                ->where('status', 'pending')
                ->where(function ($q) use ($active) {
                    $q->where('student_email', $active->student_email)
                      ->orWhere('student_phone', $active->student_phone);
                })
                ->update([
                    'status' => 'cancelled',
                    'notes' => 'Auto-cancelled: Student already has active enrollment #' . $active->id,
                ]);

            $cleanedPayments += $dupPayments;
        }

        // 2. For students with multiple pending enrollments (e.g. repeated click attempts),
        // keep only the latest pending enrollment and delete older duplicate pending rows
        $pendingGroups = Enrollment::where('status', 'pending')
            ->select('course_id', 'student_phone', DB::raw('COUNT(*) as total'), DB::raw('MAX(id) as keep_id'))
            ->groupBy('course_id', 'student_phone')
            ->having('total', '>', 1)
            ->get();

        foreach ($pendingGroups as $group) {
            $toDelete = Enrollment::where('course_id', $group->course_id)
                ->where('student_phone', $group->student_phone)
                ->where('status', 'pending')
                ->where('id', '!=', $group->keep_id)
                ->get();

            foreach ($toDelete as $dup) {
                Payment::where('enrollment_id', $dup->id)->update(['enrollment_id' => $group->keep_id]);
                $dup->delete();
                $cleanedEnrollments++;
            }
        }

        $this->info("Cleanup completed successfully!");
        $this->info("Removed {$cleanedEnrollments} duplicate pending enrollments.");
        $this->info("Cleaned {$cleanedPayments} orphaned pending payments.");

        return Command::SUCCESS;
    }
}
