<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\BookRepository\BookRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class BookStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        #[Autowire(service: ItemProvider::class)] private ProviderInterface       $itemProvider,
        private readonly BookRepositoryInterface                                  $bookRepository,
    )
    {

    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        if($operation instanceof CollectionOperationInterface) {
            $books = $this->collectionProvider->provide($operation, $uriVariables, $context);
            foreach ($books as $book) {
                $book->title = 'Title bus: ' . $book->title;
            }
            return $books;
        }
        $book = $this->itemProvider->provide($operation, $uriVariables, $context);

        $otherBook = $this->bookRepository->find($book->book);
        $book->author = $otherBook->author;

        return $book;
    }


}
