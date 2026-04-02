<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consultation\StoreConsultationRequest;
use App\Jobs\GenerateConsultationAnswerJob;
use App\Models\Consultation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function store(StoreConsultationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $consultation = Consultation::query()->create([
            'user_id' => $request->user()->id,
            'question' => $data['question'],
            'answer' => null,
            'provider' => 'openrouter',
            'model' => (string) config('services.openrouter.model'),
            'meta' => null,
            'status' => 'queued',
        ]);

        GenerateConsultationAnswerJob::dispatch($consultation->id);

        return response()->json($consultation, 202);
    }

    public function index(Request $request): JsonResponse
    {
        $items = Consultation::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(20);

        return response()->json($items);
    }
}
