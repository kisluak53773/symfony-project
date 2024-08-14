<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Order\CreateOrderDto;
use App\DTO\Order\PatchOrderDto;
use App\DTO\PaginationQueryDto;
use App\Entity\Order;

interface OrderServiceInterface
{
    public function createOrder(CreateOrderDto $createOrderDto): void;

    /**
     * Summary of getUserOrders
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{total_items: int, current_page: int, total_pages: int, data: array}
     */
    public function getUserOrders(PaginationQueryDto $paginationQueryDto): array;

    /**
     * Summary of getVendorOrders
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{total_items: int, current_page: int, total_pages: int, data: array}
     */
    public function getVendorOrders(PaginationQueryDto $paginationQueryDto): array;

    /**
     * Summary of getVendorOrderById
     * @param int $id
     * 
     * @return array{orderData: Order, products: array}
     */
    public function getVendorOrderById(int $id): array;

    /**
     * Summary of getAllOrders
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{total_items: int, current_page: int, total_pages: int, data: array}
     */
    public function getAllOrders(PaginationQueryDto $paginationQueryDto): array;

    public function patchOrder(int $id, PatchOrderDto $patchOrderDto): void;

    public function cancelOrder(int $id): void;
}
