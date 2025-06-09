<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Gedmo\Translatable\Entity\Translation;

abstract class AbstractFixtures extends Fixture
{
    public function __construct(
        private array $locales = [],
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $entities = [];
        $r = new \ReflectionClass($this->getEntityClass());

        foreach ($this->getData() as $key => $data) {
            $constructor = $r->getConstructor();
            $args = [];

            if (null === $constructor) {
                throw new \RuntimeException(\sprintf('The entity "%s" has no constructor.', $r->getName()));
            }

            foreach ($constructor->getParameters() as $parameter) {
                if (isset($data[$parameter->getName()])) {
                    $args[$parameter->getName()] = $data[$parameter->getName()];
                }
            }

            $entities[] = $entity = $r->newInstanceArgs($args);

            foreach ($data as $property => $value) {
                $setter = 'set'.ucfirst($property);

                if (method_exists($entity, $setter)) {
                    $entity->$setter($value);
                }
            }

            $this->postInstantiate($entity);
            $manager->persist($entity);
            ++$key;
            $this->addReference($r->getShortName().'_'.$key, $entity);
        }

        if ($this instanceof TranslatableFixtureInterface) {
            $translationRepository = $manager->getRepository(Translation::class);

            foreach ($this->locales as $locale) {
                foreach ($this->getDataForLocale($locale) as $key => $data) {
                    $entity = $entities[$key] ?? null;

                    if (null === $entity) {
                        throw new \RuntimeException(\sprintf('No entity found for key "%s".', $key));
                    }
                    foreach ($data as $property => $value) {
                        $translationRepository->translate($entity, $property, $locale, $value);
                    }
                }
            }
        }

        $manager->flush();
    }

    protected function postInstantiate(object $entity): void
    {
    }

    abstract protected function getData(): iterable;

    abstract protected function getEntityClass(): string;
}
