<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Doctrine\IdGenerator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

final class UuidGenerator extends AbstractIdGenerator
{
    private $protoFactory;
    private $factory;
    private $entityGetter;

    public function __construct(UuidFactory $factory = null)
    {
        $this->protoFactory = $this->factory = $factory ?? new UuidFactory();
    }

    public function generate(EntityManager $em, $entity): Uuid
    {
        if (null !== $this->entityGetter) {
            if (\is_callable([$entity, $this->entityGetter])) {
                return $this->factory->create($entity->{$this->entityGetter}());
            }

            return $this->factory->create($entity->{$this->entityGetter});
        }

        return $this->factory->create();
    }

    public function nameBased(string $entityGetter, Uuid|string|null $namespace = null): static
    {
        $clone = clone $this;
        $clone->factory = $clone->protoFactory->nameBased($namespace);
        $clone->entityGetter = $entityGetter;

        return $clone;
    }

    /**
     * @return static
     */
    public function randomBased(): self
    {
        $clone = clone $this;
        $clone->factory = $clone->protoFactory->randomBased();
        $clone->entityGetter = null;

        return $clone;
    }

    public function timeBased(Uuid|string|null $node = null): static
    {
        $clone = clone $this;
        $clone->factory = $clone->protoFactory->timeBased($node);
        $clone->entityGetter = null;

        return $clone;
    }
}
