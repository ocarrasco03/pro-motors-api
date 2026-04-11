<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Scheduler\ScheduledTaskController;
use App\Http\Controllers\Api\V1\Settings\CompanyController;
use App\Http\Controllers\Api\V1\Settings\UserController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

Route::fallback(function () {
    throw new NotFoundHttpException;
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['prefix' => 'support'], function () {
            Route::group(['prefix' => 'scheduler'], function () {
                Route::group(['prefix' => 'tasks'], function () {
                    Route::get('/', [ScheduledTaskController::class, 'index']);
                    Route::post('/', [ScheduledTaskController::class, 'store']);
                    Route::get('/{scheduledTask}', [ScheduledTaskController::class, 'show']);
                    Route::put('/{scheduledTask}', [ScheduledTaskController::class, 'update']);
                    Route::delete('/{scheduledTask}', [ScheduledTaskController::class, 'destroy']);
                    Route::patch('/{scheduledTask}/toggle', [ScheduledTaskController::class, 'toggle']);
                });
            });
        });

        Route::group(['prefix' => 'settings'], function () {
            Route::group(['prefix' => 'companies'], function () {
                Route::get('/', [CompanyController::class, 'index']);
                Route::post('/', [CompanyController::class, 'store']);
                Route::get('/{company}', [CompanyController::class, 'show']);
                Route::put('/{company}', [CompanyController::class, 'update']);
                Route::delete('/{company}', [CompanyController::class, 'destroy']);
            });
            Route::group(['prefix' => 'users'], function () {
                Route::get('/', [UserController::class, 'index']);
                Route::post('/', [UserController::class, 'store']);
                Route::get('/{user}', [UserController::class, 'show']);
                Route::put('/{user}', [UserController::class, 'update']);
                Route::delete('/{user}', [UserController::class, 'destroy']);
                Route::patch('/{user}/toggle', [UserController::class, 'toggle']);
            });
        });

        Route::group(['prefix' => 'catalog'], function () {
            Route::group(['prefix' => 'brands'], function () {
                Route::get('/', [BrandController::class, 'index']);
                Route::post('/', [BrandController::class, 'store']);
                Route::get('/{brand}', [BrandController::class, 'show']);
                Route::put('/{brand}', [BrandController::class, 'update']);
                Route::delete('/{brand}', [BrandController::class, 'destroy']);
            });

            Route::group(['prefix' => 'suppliers'], function () {
                Route::get('/', [SupplierController::class, 'index']);
                Route::post('/', [SupplierController::class, 'store']);
                Route::get('/{supplier}', [SupplierController::class, 'show']);
                Route::put('/{supplier}', [SupplierController::class, 'update']);
                Route::delete('/{supplier}', [SupplierController::class, 'destroy']);
            });

            Route::group(['prefix' => 'products'], function () {
                Route::get('/', [ProductController::class, 'index']);
                Route::post('/', [ProductController::class, 'store']);
                Route::get('/{product}', [ProductController::class, 'show']);
                Route::put('/{product}', [ProductController::class, 'update']);
                Route::delete('/{product}', [ProductController::class, 'destroy']);
                Route::post('/{product}/equivalences', [ProductController::class, 'addEquivalence']);
                Route::delete('/{product}/equivalences/{equivalentId}', [ProductController::class, 'removeEquivalence']);
                Route::post('/{product}/relations', [ProductController::class, 'addRelation']);
                Route::delete('/{product}/relations/{relatedId}', [ProductController::class, 'removeRelation']);
            });

            Route::group(['prefix' => 'price-lists'], function () {
                Route::get('/', [PriceListController::class, 'index']);
                Route::post('/', [PriceListController::class, 'store']);
                Route::get('/{priceList}', [PriceListController::class, 'show']);
                Route::put('/{priceList}', [PriceListController::class, 'update']);
                Route::delete('/{priceList}', [PriceListController::class, 'destroy']);
                Route::post('/{priceList}/products', [PriceListController::class, 'setProductPrice']);
                Route::delete('/{priceList}/products/{productId}', [PriceListController::class, 'removeProductPrice']);
            });
        });
    });
});
