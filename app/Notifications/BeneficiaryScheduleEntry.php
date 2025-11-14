<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Candidate;

class BeneficiaryScheduleEntry extends Notification
{
    use Queueable;

    protected $candidate;
    protected $programName;
    protected $entryDate;
    protected $observations;

    public function __construct(Candidate $candidate, string $programName, string $entryDate, ?string $observations = null)
    {
        $this->candidate = $candidate;
        $this->programName = $programName;
        $this->entryDate = $entryDate;
        $this->observations = $observations;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Ingreso programado',
            'description' => "El beneficiario {$this->candidate->full_name} debe ser ingresado al programa {$this->programName} el día {$this->entryDate}. Observaciones: {$this->observations}",
            'candidate_id' => $this->candidate->id,
        ];
    }
}
