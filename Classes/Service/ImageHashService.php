<?php
declare(strict_types=1);

namespace Flowpack\SimilarAssets\Service;

use Flowpack\SimilarAssets\Domain\Model\ImageHash;
use Flowpack\SimilarAssets\Domain\Repository\ImageHashRepository;
use Jenssegers\ImageHash\Hash;
use Jenssegers\ImageHash\ImageHash as ImageHasher;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Media\Domain\Model\AssetInterface;
use Neos\Media\Domain\Model\Image;
use phpseclib3\Math\BigInteger;

/**
 * @Flow\Scope("singleton")
 */
class ImageHashService
{
    /**
     * @var ImageHasher
     */
    protected $hasher;

    /**
     * @Flow\Inject
     * @var ImageHashRepository
     */
    protected $imageHashRepository;

    public function __construct()
    {
        $this->hasher = new ImageHasher(new DifferenceHash(8));
    }

    public function getHashes(): QueryResultInterface
    {
        return $this->imageHashRepository->findAll();
    }

    public function generateHashForAsset(AssetInterface $asset): ?string
    {
        if ($asset instanceof Image) {
            return $this->generateHashForImage($asset);
        }
        return null;
    }

    public function generateHashForImage(Image $image): string
    {
        $hash = $this->generateHash($image->getResource()->getStream());

        /** @var ImageHash $existingHash */
        $existingHash = $this->imageHashRepository->findOneByImageId($image->getIdentifier());
        if (!$existingHash) {
            $this->imageHashRepository->add(new ImageHash($hash, $image->getIdentifier()));
        } elseif ($existingHash->getHash() !== $hash) {
            $existingHash->setHash($hash);
            $this->imageHashRepository->update($existingHash);
        }

        return $hash;
    }

    /**
     * Creates a hash for a given image path or resource stream
     *
     * @param string|resource $pathOrResource
     * @return string big integer hash
     */
    public function generateHash($pathOrResource): string
    {
        return (string)new BigInteger($this->hasher->hash($pathOrResource)->toHex(), 16);
    }

    public function removeHashForAsset(AssetInterface $asset): void
    {
        if ($asset instanceof Image) {
            $this->removeHashForImage($asset);
        }
    }

    public function removeHashForImage(Image $image): void
    {
        /** @var ImageHash $existingHash */
        $existingHash = $this->imageHashRepository->findOneByImageId($image->getIdentifier());
        if ($existingHash) {
            $this->removeHash($existingHash);
        }
    }

    public function removeHash(ImageHash $imageHash): void
    {
        $this->imageHashRepository->remove($imageHash);
    }

    /**
     * @param string $hashToCompare
     * @return array<ImageHash>
     */
    public function findSimilarHashes(string $hashToCompare): array
    {
        return $this->imageHashRepository->findSimilarByHash($hashToCompare);
    }

    public function getSimilarity(string $a, string $b): int
    {
        $hexHashA = (new BigInteger($a, 10))->toHex();
        $hexHashB = (new BigInteger($b, 10))->toHex();
        return $this->hasher->distance(Hash::fromHex($hexHashA), Hash::fromHex($hexHashB));
    }
}
