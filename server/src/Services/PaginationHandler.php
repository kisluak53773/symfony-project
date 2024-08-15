<?php

declare(strict_types=1);

namespace App\Services;

use Knp\Component\Pager\PaginatorInterface;
use App\DTO\PaginationQueryDto;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Contract\PaginationHandlerInterface;

/**
 * @template T
 * @implements PaginationHandlerInterface<T>
 */
class PaginationHandler implements PaginationHandlerInterface
{
    public function __construct(private PaginatorInterface $paginator) {}

    /**
     * Summary of getSearchResults
     * @param mixed $querryBuilder
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return PaginationInterface<int, mixed>
     */
    private function getSearchResults(mixed $querryBuilder, PaginationQueryDto $paginationQueryDto): PaginationInterface
    {
        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $paginationQueryDto->page,
            $paginationQueryDto->limit
        );

        return $pagination;
    }


    /**
     * Summary of cunstructResponse
     * @param \Knp\Component\Pager\Pagination\PaginationInterface<int, mixed> $pagination
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    private function cunstructResponse(PaginationInterface $pagination): array
    {
        $products = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = (int)ceil($totalItems / $itemsPerPage);

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $products,
        ];

        return $response;
    }

    /**
     * @param mixed $querryBuilder
     * @param PaginationQueryDto $paginationQueryDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    public function handlePagination(mixed $querryBuilder, PaginationQueryDto $paginationQueryDto): array
    {
        $pagination = $this->getSearchResults($querryBuilder, $paginationQueryDto);
        $response = $this->cunstructResponse($pagination);

        return $response;
    }
}
