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

    public function updateFoodLog(Request $request)
    {
        $data = $request->validate([
            'lifestylies' => 'required|array|min:1',
            'date' => 'required|date',
            'lifestylies.*.life_style_behavior_id' => 'required|exists:life_style_behaviors,id',
            'lifestylies.*.value' => 'required',
        ]);

        $log = $this->lifeStyleService->updateLifeStyleLog($data['lifestylies'],$data['date']);


        return response()->data($log, ' Lifestyle log updated successfully.');

    }

    public function getScoreStats(Request $request)
    {
        $fromDate = $request->input('from_date');
        $endDate = $request->input('end_date');

        return response()->data($this->lifeStyleService->getLifeStyleScoreByPeriod($fromDate, $endDate));
    }

    public function getLifeStyleLog(Request $request)
    {

        $data = $this->lifeStyleService->getLifeStyleLog($request->query('date'));
        return response()->data($data,ResponseMessages::INDEX_SUCCESS);
    }
}
