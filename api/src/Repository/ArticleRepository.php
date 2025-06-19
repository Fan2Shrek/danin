<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findOneBySlugForLocale(string $slug): ?Article
    {
        $dql = <<<DQL
SELECT a
FROM %s a
WHERE a.slug = :slug
DQL;
        $query = $this->getEntityManager()->createQuery(\sprintf($dql, $this->getEntityName()));
        $query->setParameter('slug', $slug);
        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            TranslationWalker::class,
        );

        return $query->getOneOrNullResult();
    }
}
