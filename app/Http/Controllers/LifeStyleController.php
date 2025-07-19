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
            'date' => 'required|date',
            'lifestylies' => 'required|array|min:1',
            'lifestylies.*.life_style_behavior_id' => 'required|exists:life_style_behaviors,id',
            'lifestylies.*.value' => 'required',
        ]);

        $logs = $this->lifeStyleService->logLifeStyle($validated['lifestylies'],$validated['date']);

        return response()->data($logs, 'Lifestyle logged successfully.');
    }

    public function getScoreStats(Request $request)
    {
        $type = $request->query('type', 'day');
        $date = $request->query('date', now()->toDateString());

        return response()->data($this->lifeStyleService->getLifeStyleScoreByPeriod($date, $type));
    }

    public function getLifeStyleLog(Request $request)
    {

        $data = $this->lifeStyleService->getLifeStyleLog($request->query('date'));
        return response()->data($data,ResponseMessages::INDEX_SUCCESS);
    }
}
