<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bazi\CalculateRequest;
use App\Models\HoroscopeRecord;
use App\Services\BaziService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HoroscopeController extends Controller
{
    public function __construct(private readonly BaziService $baziService)
    {
    }

    public function calculate(CalculateRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $result = $this->baziService->calculate($payload);

        $record = HoroscopeRecord::query()->create([
            'user_id' => $request->user()->id,
            'birth_date' => $payload['birth_date'],
            'birth_time' => $payload['birth_time'],
            'gender' => $payload['gender'],
            'timezone' => $payload['timezone'] ?? 'UTC',
            'result' => $result,
        ]);

        return response()->json([
            'record_id' => $record->id,
            'result' => $result,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $records = HoroscopeRecord::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(20);

        return response()->json($records);
    }
}
