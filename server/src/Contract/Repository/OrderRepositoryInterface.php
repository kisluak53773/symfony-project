<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use Doctrine\ORM\QueryBuilder;
use App\Entity\User;
use App\Entity\Vendor;
use App\DTO\Order\CreateOrderDto;
use App\DTO\Order\PatchOrderDto;
use App\Entity\Order;

interface OrderRepositoryInterface
{
    public function createQuerryBuilderForPagination(): QueryBuilder;

    public function getAllOrdersBelonignToUser(User $user): QueryBuilder;

    public function create(CreateOrderDto $createOrderDto, User $user): Order;

    public function patch(PatchOrderDto $patchOrderDto, Order $order): void;

    public function cancel(Order $order): void;

    public function createQuerryBuilderForVendorAndPagination(Vendor $vendor): QueryBuilder;
}
