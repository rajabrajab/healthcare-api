<?php

/*
*   Developer : Abd alwahed rajab
*/

// Run
// php artisan make:provider ResponseServiceProvider
namespace App\Providers;

use App\Http\Helpers\HttpCodes;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {

        $factory->macro('data', function ($data = false, $message = '') use ($factory) {

            $executionEndTime = microtime(true);
            $seconds = $executionEndTime - LARAVEL_START;
            $seconds = number_format($seconds, 3) . ' seconds';

            $format = [
                'state' => true,
                'code' => HttpCodes::OK,
                'message' => $message,
                'execution' => $seconds
            ];

            $format['data'] = $data ?? [];



            if (env('FULL_SYSTEM_DEBUG') == 'true') {
                $debug = debug_request();
                $format['debug'] = $debug;
            }

            return $factory->make($format);
        });


        $factory->macro('message', function ($message) use ($factory) {

            $executionEndTime = microtime(true);
            $seconds = $executionEndTime - LARAVEL_START;
            $seconds = number_format($seconds, 3) . ' seconds';

            $format = [
                'state' => true,
                'code' => HttpCodes::OK,
                'message' => $message,
                'execution' => $seconds
            ];


            $format['data'] = $data ?? [];

            if (env('FULL_SYSTEM_DEBUG') == 'true') {
                $debug = debug_request();
                $format['debug'] = $debug;
            }


            return $factory->make($format);
        });


        $factory->macro('notFound', function ($message) use ($factory) {

            $executionEndTime = microtime(true);
            $seconds = $executionEndTime - LARAVEL_START;
            $seconds = number_format($seconds, 3) . ' seconds';

            $format = [
                'state' => true,
                'code' => HttpCodes::NOT_FOUND,
                'message' => $message . ' not found',
                'execution' => $seconds
            ];



            if (env('FULL_SYSTEM_DEBUG') == 'true') {
                $debug = debug_request();
                $format['debug'] = $debug;
            }


            return $factory->make($format);
        });


        $factory->macro('success', function ($message = '', $data = false) use ($factory) {

            $executionEndTime = microtime(true);
            $seconds = $executionEndTime - LARAVEL_START;
            $seconds = number_format($seconds, 3) . ' seconds';

            $format = [
                'state' => true,
                'code' => HttpCodes::OK,
                'message' => $message,
                'execution' => $seconds
            ];
            $format['data'] = $data ?? [];

            if (env('FULL_SYSTEM_DEBUG') == 'true') {
                $debug = debug_request();
                $format['debug'] = $debug;
            }

            return $factory->make($format);
        });




        $factory->macro('error', function ($code, $message = '', $data = []) use ($factory) {

            $executionEndTime = microtime(true);
            $seconds = $executionEndTime - LARAVEL_START;
            $seconds = number_format($seconds, 3) . ' seconds';

            $false = [
                'state' => false,
                'code' => $code,
                'message' => $message,
                'execution' => $seconds,
            ];

            if ($data) {
                $false['data'] = $data;
            }

            if (env('FULL_SYSTEM_DEBUG') == 'true') {
                $debug = debug_request();
                $false['debug'] = $debug;
            }

            return $factory->make($false,$code);
        });
    }






    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
