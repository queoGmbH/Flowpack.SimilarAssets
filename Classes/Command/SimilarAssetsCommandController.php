<?php
declare(strict_types=1);

namespace Flowpack\SimilarAssets\Command;

/*
 * This file is part of the Flowpack.SimilarAssets package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\SimilarAssets\Domain\Model\ImageHash;
use Flowpack\SimilarAssets\Service\ImageHashService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Repository\ImageRepository;

class SimilarAssetsCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var ImageRepository
     */
    protected $imageRepository;

    /**
     * @Flow\Inject
     * @var ImageHashService
     */
    protected $imageHashService;

    public function generateImageHashesCommand(bool $force = false): void
    {
        $this->outputLine('Generating hashes for images');

        $imageIterator = $this->imageRepository->findAll();

        $hashesByImage = [];
        /** @var ImageHash $imageHash */
        foreach ($this->imageHashService->getHashes() as $imageHash) {
            $hashesByImage[$imageHash->getImageId()] = $imageHash;
        }

        $this->outputLine('Found %d existing hashes', [count($hashesByImage)]);

        $this->outputLine('Verifying hash for each image');

        $this->output->progressStart($this->imageRepository->countAll());

        /** @var Image $image */
        while ($image = $imageIterator->current()) {
            $this->output->progressAdvance();
            $imageIterator->next();

            $imageId = $image->getIdentifier();
            if (array_key_exists($imageId, $hashesByImage)) {
                unset($hashesByImage[$imageId]);
                if (!$force) {
                    continue;
                }
            }

            $this->imageHashService->generateHashForImage($image);
        }
        $this->output->progressFinish();
        $this->outputLine();


        $this->outputLine('Removing %d hashes for non existing images', [count($hashesByImage)]);
        foreach ($hashesByImage as $imageHash) {
            $this->imageHashService->removeHash($imageHash);
        }
    }

    public function findSimilarImagesCommand(string $pathToImage): void
    {
        $hashToCompare = $this->imageHashService->generateHash($pathToImage);
        $similarHash = $this->imageHashService->findSimilarHashes($hashToCompare);

        $this->outputLine('Hash for "%s" is "%s"', [$pathToImage, $hashToCompare]);
        $this->outputLine();
        $this->outputLine('Similar images:');

        $rows = [];

        foreach ($similarHash as $imageHash) {
            /** @var Image $image */
            $image = $this->imageRepository->findByIdentifier($imageHash->getImageId());

            $distance = $this->imageHashService->getSimilarity($hashToCompare, $imageHash->getHash());

            $rows[] = [
                $image->getLabel(),
                $imageHash->getHash(),
                $distance,
            ];
        }

        $this->output->outputTable($rows, [
            'Image',
            'Hash',
            'Distance'
        ]);
    }
}
