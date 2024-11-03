<?php

namespace App\Serializer;


use ApiPlatform\State\SerializerContextBuilderInterface;
use App\Entity\Book;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AsDecorator(decorates: 'api_platform.serializer.context_builder')]
readonly class BookContextBuilder implements SerializerContextBuilderInterface
{

    public function __construct(
        #[AutowireDecorated]
        private SerializerContextBuilderInterface $decorated,
        private AuthorizationCheckerInterface     $authorizationChecker
    )
    {
    }


    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {

        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;
        if ($resourceClass === Book::class && isset($context['groups']) && $this->authorizationChecker->isGranted('OIDC_ADMIN')
            && true === $normalization) {
            $context['groups'][] = 'custom:output';
        }

        return $context;

    }
}
