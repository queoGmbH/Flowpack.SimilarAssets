<?php
declare(strict_types=1);

namespace Flowpack\SimilarAssets;

/*
 * This file is part of the Flowpack.SimilarAssets package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\Neos\AssetUsage\Service\AssetIntegrationService;
use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Model\Workspace;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\Media\Domain\Service\AssetService;

class Package extends BasePackage
{

    public function boot(Bootstrap $bootstrap): void
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(AssetService::class, 'assetRemoved', AssetIntegrationService::class, 'removeHashForAsset');
        $dispatcher->connect(AssetService::class, 'assetCreated', AssetIntegrationService::class, 'generateHashForAsset');
        $dispatcher->connect(AssetService::class, 'assetResourceReplaced', AssetIntegrationService::class, 'generateHashForAsset');
    }
}
