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

    public function deleteFromUserDiet($foodId)
    {
        $this->foodService->deleteFromUserDiet($foodId);

        return response()->data(ResponseMessages::DELETE_SUCCESS);
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
            'foods' => 'required|array|min:1',
            'foods.*.food_id' => 'required|exists:foods,id',
            'foods.*.quantity' => 'required|integer|min:1',
        ]);

        $log = $this->foodService->logFoodIntake($data['foods']);


        return response()->data($log, 'Food logged successfully.');
    }

    public function getFoodLog(Request $request)
    {

        $data = $this->foodService->getFoodLog($request->query('date'));
        return response()->data($data,ResponseMessages::INDEX_SUCCESS);
    }

    public function getScoreStats(Request $request)
    {
        $type = $request->query('type', 'day');
        $date = $request->query('date', now()->toDateString());

        return response()->data(
            $this->foodService->getDietScoreByPeriod($date, $type)
        );
    }
}
