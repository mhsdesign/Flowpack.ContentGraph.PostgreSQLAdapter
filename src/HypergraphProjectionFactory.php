<?php

declare(strict_types=1);

namespace Flowpack\ContentGraph\PostgreSQLAdapter;

use Flowpack\ContentGraph\PostgreSQLAdapter\Domain\Projection\HypergraphProjection;
use Flowpack\ContentGraph\PostgreSQLAdapter\Domain\Repository\NodeFactory;
use Flowpack\ContentGraph\PostgreSQLAdapter\Infrastructure\PostgresDbalClientInterface;
use Neos\ContentRepository\Core\ContentGraphFinder;
use Neos\ContentRepository\Core\Factory\ProjectionFactoryDependencies;
use Neos\ContentRepository\Core\Projection\ProjectionFactoryInterface;
use Neos\ContentRepository\Core\SharedModel\ContentRepository\ContentRepositoryId;

/**
 * @implements ProjectionFactoryInterface<HypergraphProjection>
 * @api
 */
final class HypergraphProjectionFactory implements ProjectionFactoryInterface
{
    public function __construct(
        private readonly PostgresDbalClientInterface $dbalClient
    ) {
    }

    public static function graphProjectionTableNamePrefix(
        ContentRepositoryId $contentRepositoryId
    ): string {
        return sprintf('cr_%s_p_hypergraph', $contentRepositoryId->value);
    }

    public function build(
        ProjectionFactoryDependencies $projectionFactoryDependencies,
        array $options,
    ): HypergraphProjection {
        $tableNamePrefix = self::graphProjectionTableNamePrefix(
            $projectionFactoryDependencies->contentRepositoryId
        );

        $nodeFactory = new NodeFactory(
            $projectionFactoryDependencies->contentRepositoryId,
            $projectionFactoryDependencies->nodeTypeManager,
            $projectionFactoryDependencies->propertyConverter
        );

        return new HypergraphProjection(
            $this->dbalClient,
            $tableNamePrefix,
            new ContentGraphFinder(new ContentHyperGraphFactory($this->dbalClient, $nodeFactory, $projectionFactoryDependencies->contentRepositoryId, $projectionFactoryDependencies->nodeTypeManager, $tableNamePrefix))
        );
    }
}
