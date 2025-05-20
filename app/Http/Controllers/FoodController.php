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

    public function foodsByBategory(FoodService $foodService)
    {
        $data = $foodService->getFoodsGroupedByCategory();

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
}
