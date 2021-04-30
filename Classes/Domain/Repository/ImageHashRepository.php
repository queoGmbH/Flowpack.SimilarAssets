<?php
declare(strict_types=1);

namespace Flowpack\SimilarAssets\Domain\Repository;

/*
 * This file is part of the Flowpack.SimilarAssets package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Flowpack\SimilarAssets\Domain\Model\ImageHash;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 *
 * @method ImageHash findOneByHash(string $hash)
 * @method ImageHash|null findOneByImageId(string $imageId)
 */
class ImageHashRepository extends Repository
{

    /**
     * @Flow\Inject
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param string $imageHash
     * @param int $maxDistance
     * @return array<ImageHash>
     */
    public function findSimilarByHash(string $imageHash, int $maxDistance = 15): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(ImageHash::class, 'h');
        $rsm->addFieldResult('h', 'imageid', 'imageId');
        $rsm->addFieldResult('h', 'hash', 'hash');

        $queryString = '
            SELECT imageid, hash
            FROM flowpack_similarassets_domain_model_imagehash
            WHERE BIT_COUNT(hash ^ ?) < ?
        ';
        $query = $this->entityManager->createNativeQuery($queryString, $rsm)
            ->setParameter(1, $imageHash)
            ->setParameter(2, $maxDistance);

        // TODO: Add query variant for PostgreSQL similar to https://stackoverflow.com/questions/46280722/bit-count-function-in-postgresql or https://github.com/jenssegers/imagehash/issues/45

        return $query->execute();
    }

    /**
     * @param string $imageId
     * @param int $maxDistance
     * @return array<ImageHash>
     */
    public function findSimilarByImageId(string $imageId, int $maxDistance = 15): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(ImageHash::class, 'h');
        $rsm->addFieldResult('h', 'imageid', 'imageId');
        $rsm->addFieldResult('h', 'hash', 'hash');

        $queryString = '
            SELECT a.imageid, a.hash
            FROM flowpack_similarassets_domain_model_imagehash a
            JOIN flowpack_similarassets_domain_model_imagehash b
            WHERE b.imageid != a.imageid AND b.imageid = ? AND BIT_COUNT(a.hash ^ b.hash) < ?
        ';
        $query = $this->entityManager->createNativeQuery($queryString, $rsm)
            ->setParameter(1, $imageId)
            ->setParameter(2, $maxDistance);

        // TODO: Add query variant for PostgreSQL similar to https://stackoverflow.com/questions/46280722/bit-count-function-in-postgresql or https://github.com/jenssegers/imagehash/issues/45

        return $query->execute();
    }

}
