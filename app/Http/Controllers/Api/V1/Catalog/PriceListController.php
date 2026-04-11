<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\PriceList\CreatePriceListDTO;
use App\Application\DTOs\PriceList\SetProductPriceDTO;
use App\Application\DTOs\PriceList\UpdatePriceListDTO;
use App\Application\Services\Product\PriceListServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Product\PriceListRequest;
use App\Http\Requests\Product\PriceListUpdateRequest;
use App\Http\Requests\Product\SetProductPriceRequest;
use App\Http\Resources\Product\PriceListCollection;
use App\Http\Resources\Product\PriceListResource;
use App\Models\PriceList;
use Illuminate\Http\JsonResponse;

class PriceListController extends Controller
{
    public function __construct(
        protected PriceListServiceInterface $priceListService
    ) {
        $this->authorizeResource(PriceList::class, 'priceList');
    }

    public function index(SearchRequest $request)
    {
        $dto = SearchDTO::fromArray($request->validated());
        $priceLists = $this->priceListService->getAllForCompany(auth()->user(), $dto);

        return $this->success(new PriceListCollection($priceLists));
    }

    public function store(PriceListRequest $request)
    {
        $dto = CreatePriceListDTO::fromArray($request->validated());
        $priceList = $this->priceListService->create($dto);

        return $this->success(new PriceListResource($priceList), 'Price list has been created.', 201);
    }

    public function show(PriceList $priceList)
    {
        $this->authorize('view', $priceList);
        $priceList = $this->priceListService->getPriceList($priceList);

        return $this->success(new PriceListResource($priceList));
    }

    public function update(PriceListUpdateRequest $request, PriceList $priceList)
    {
        $this->authorize('update', $priceList);
        $dto = UpdatePriceListDTO::fromArray($request->validated());
        $result = $this->priceListService->update($priceList, $dto);

        return $this->success(new PriceListResource($result), 'Price list updated successfully.');
    }

    public function destroy(PriceList $priceList)
    {
        $this->authorize('delete', $priceList);
        $this->priceListService->delete($priceList);

        return $this->success(null, 'Price list has been deleted.', 204);
    }

    public function setProductPrice(SetProductPriceRequest $request, PriceList $priceList): JsonResponse
    {
        $this->authorize('managePrices', $priceList);
        $dto = SetProductPriceDTO::fromArray($request->validated());
        $priceList = $this->priceListService->setProductPrice($priceList, $dto);

        return $this->success(new PriceListResource($priceList), 'Product price set successfully.');
    }

    public function removeProductPrice(PriceList $priceList, int $productId): JsonResponse
    {
        $this->authorize('managePrices', $priceList);
        $this->priceListService->removeProductPrice($priceList, $productId);

        return $this->success(null, 'Product price removed successfully.');
    }
}
