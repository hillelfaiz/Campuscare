<?php

namespace App\Mail;

use App\Models\FacilityReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $report;

    public function __construct(FacilityReport $report)
    {
        $this->report = $report;
    }

    public function build()
    {
        return $this->subject('Status Laporan Kamu Diperbarui')
            ->view('emails.report_status_updated');
    }
}