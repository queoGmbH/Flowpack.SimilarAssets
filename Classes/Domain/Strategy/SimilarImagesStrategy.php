<?php
declare(strict_types=1);

namespace Flowpack\SimilarAssets\Domain\Strategy;

/*
 * This file is part of the Flowpack.SimilarAssets package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\SimilarAssets\Domain\Repository\ImageHashRepository;
use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\AssetInterface;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Repository\ImageRepository;

if (interface_exists('Flowpack\Media\Ui\Domain\Strategy\AssetSimilarityStrategyInterface', false)) {
    /**
     * This strategy provides similar assets for the Neos Media Ui
     *
     * @Flow\Scope("singleton")
     */
    final class SimilarImagesStrategy implements \Flowpack\Media\Ui\Domain\Strategy\AssetSimilarityStrategyInterface
    {

        /**
         * @Flow\Inject
         * @var ImageHashRepository
         */
        protected $imageHashRepository;

        /**
         * @Flow\Inject
         * @var ImageRepository
         */
        protected $imageRepository;

        public function hasSimilarAssets(AssetInterface $asset): bool
        {
            return $this->getSimilarAssetCount($asset) > 0;
        }

        public function getSimilarAssetCount(AssetInterface $asset): int
        {
            if (!$asset instanceof Image) {
                return 0;
            }
            // TODO: Implement count as query
            return count($this->imageHashRepository->findSimilarByImageId($asset->getIdentifier()));
        }

        public function getSimilarAssets(AssetInterface $asset): array
        {
            if (!$asset instanceof Image) {
                return [];
            }
            $hashes = $this->imageHashRepository->findSimilarByImageId($asset->getIdentifier());

            return array_map(function ($hash) {
                return $this->imageRepository->findByIdentifier($hash->getImageId());
            }, $hashes);
        }
    }
} else {
    final class SimilarImagesStrategy {}
}
