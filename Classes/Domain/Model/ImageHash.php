<?php
declare(strict_types=1);

namespace Flowpack\SimilarAssets\Domain\Model;

/*
 * This file is part of the Flowpack.SimilarAssets package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 */
class ImageHash
{
    /**
     * @Flow\Identity
     * @ORM\Id
     * @var string
     */
    protected $imageId;

    /**
     * @ORM\Column(type="bigint")
     * @var string
     */
    protected $hash;

    public function __construct(string $hash, string $imageId)
    {
        $this->hash = $hash;
        $this->imageId = $imageId;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }

}
