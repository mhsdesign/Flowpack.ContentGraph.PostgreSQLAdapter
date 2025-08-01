<?php

declare(strict_types=1);

namespace Flowpack\ContentGraph\PostgreSQLAdapter;

use Doctrine\DBAL\Connection;
use Flowpack\ContentGraph\PostgreSQLAdapter\Domain\Projection\PostgresContentGraphProjection;
use Flowpack\ContentGraph\PostgreSQLAdapter\Domain\Repository\NodeFactory;
use Neos\ContentRepository\Core\Factory\SubscriberFactoryDependencies;
use Neos\ContentRepository\Core\Projection\ContentGraph\ContentGraphProjectionFactoryInterface;

/**
 * @api
 */
final readonly class PostgresContentGraphProjectionFactory implements ContentGraphProjectionFactoryInterface
{
    public function __construct(
        private Connection $dbal,
    ) {
    }

    public function build(
        SubscriberFactoryDependencies $projectionFactoryDependencies,
    ): PostgresContentGraphProjection {
        $nodeFactory = new NodeFactory(
            $projectionFactoryDependencies->contentRepositoryId,
            $projectionFactoryDependencies->getPropertyConverter()
        );

        return new PostgresContentGraphProjection(
            $this->dbal,
            $projectionFactoryDependencies->contentRepositoryId,
            new ContentHyperGraphReadModelAdapter(
                $this->dbal,
                $projectionFactoryDependencies->getPropertyConverter(),
                $nodeFactory,
                $projectionFactoryDependencies->contentRepositoryId,
                $projectionFactoryDependencies->nodeTypeManager
            )
        );
    }
}
