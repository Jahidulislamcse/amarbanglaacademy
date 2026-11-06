<?php

namespace App\Console\Commands;

use App\Http\Controllers\AdminController;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;

class SyncEnrolledStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:sync-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile completed payments, ensure active enrollments, and link student user accounts for LMS & attendance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting student enrollment and user account reconciliation...');

        $courses = Course::all();
        if ($courses->isEmpty()) {
            $this->warn('No courses found.');
            return 0;
        }

        foreach ($courses as $course) {
            $this->line("Reconciling course #{$course->id}: {$course->title}...");
            AdminController::reconcileEnrolledStudents($course);
        }

        $totalActive = Enrollment::whereIn('status', ['active', 'completed'])->count();
        $totalPayments = Payment::where('status', 'completed')->count();
        $totalUsers = User::where('role', 'student')->count();

        $this->info("Reconciliation complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Completed Payments', $totalPayments],
                ['Active Enrollments', $totalActive],
                ['Student LMS Users', $totalUsers],
            ]
        );

        return 0;
    }
}
