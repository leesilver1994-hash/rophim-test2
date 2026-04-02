<?php

namespace App\Jobs;

use App\Models\Consultation;
use App\Services\AI\OpenRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateConsultationAnswerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $consultationId)
    {
    }

    public function handle(OpenRouterService $openRouterService): void
    {
        $consultation = Consultation::query()->find($this->consultationId);

        if (! $consultation) {
            return;
        }

        $consultation->update(['status' => 'processing']);

        $reply = $openRouterService->ask($consultation->question);

        $consultation->update([
            'answer' => $reply['answer'] ?? '',
            'provider' => 'openrouter',
            'model' => (string) config('services.openrouter.model'),
            'meta' => ['raw' => $reply['raw'] ?? null],
            'status' => 'done',
        ]);
    }

    public function failed(?\Throwable $exception): void
    {
        $consultation = Consultation::query()->find($this->consultationId);

        if (! $consultation) {
            return;
        }

        $consultation->update([
            'status' => 'failed',
            'meta' => [
                'error' => $exception?->getMessage(),
            ],
        ]);
    }
}
