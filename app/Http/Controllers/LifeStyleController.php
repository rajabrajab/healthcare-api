<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessages;
use App\Services\LifeStyleService;
use Illuminate\Http\Request;

class LifeStyleController extends Controller
{
    private $lifeStyleService;

    public function __construct(LifeStyleService $lifeStyleService)
    {
        $this->lifeStyleService = $lifeStyleService;
    }

    public function index()
    {
        $data = $this->lifeStyleService->getLifeStyle();

        return response()->data($data,ResponseMessages::INDEX_SUCCESS);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lifestylies' => 'required|array|min:1',
            'lifestylies.*.life_style_behavior_id' => 'required|exists:life_style_behaviors,id',
            'lifestylies.*.value' => 'required',
        ]);

        $logs = $this->lifeStyleService->logLifeStyle($validated['lifestylies']);

        return response()->data($logs, 'Lifestyle logged successfully.');
    }

    public function getDailyScoreByHour(Request $request)
    {
        return response()->data($this->lifeStyleService->getDailyLifeStyleScoreByHour($request->query('date')));
    }
}
