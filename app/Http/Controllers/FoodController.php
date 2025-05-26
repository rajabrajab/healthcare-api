<?php

namespace App\Http\Controllers;

use App\Constants\ResponseMessages;
use App\Services\FoodService;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    private $foodService;

    public function __construct(FoodService $foodService)
    {
        $this->foodService = $foodService;
    }

    public function foodsByBategory(Request $request)
    {
        $search = $request->query('search');
        $data = $this->foodService->getFoodsGroupedByCategory($search);

        return response()->data($data,ResponseMessages::INDEX_SUCCESS);
    }

    public function userDiet()
    {
        $data = $this->foodService->getUserDiet();
        return response()->data($data, ResponseMessages::INDEX_SUCCESS);
    }

    public function addFoodToDiet(Request $request)
    {
        $validated = $request->validate([
            'food_ids' => 'required|array',
            'food_ids.*' => 'exists:foods,id',
        ]);

        $this->foodService->addFoodToUserDiet($validated['food_ids']);

        return response()->data('User Diet updated successfully.');
    }

    public function logFood(Request $request)
    {
        $data = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $log = $this->foodService->logFoodIntake($data);


        return response()->data($log, 'Food logged successfully.');
    }

    public function getDailyScoreByHour()
    {
        return response()->data($this->foodService->getDailyDietScoreByHour());
    }
}
