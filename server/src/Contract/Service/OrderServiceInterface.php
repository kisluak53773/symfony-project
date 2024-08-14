<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Order\CreateOrderDto;
use App\DTO\Order\PatchOrderDto;
use App\DTO\PaginationQueryDto;

interface OrderServiceInterface
{
    public function createOrder(CreateOrderDto $createOrderDto): void;

    public function getUserOrders(PaginationQueryDto $paginationQueryDto): array;

    public function getVendorOrders(PaginationQueryDto $paginationQueryDto): array;

    public function getVendorOrderById(int $id): array;

    public function getAllOrders(PaginationQueryDto $paginationQueryDto): array;

    public function patchOrder(int $id, PatchOrderDto $patchOrderDto): void;

    public function cancelOrder(int $id): void;
}
