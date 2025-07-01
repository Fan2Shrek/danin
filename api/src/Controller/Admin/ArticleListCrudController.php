<?php

namespace App\Controller\Admin;

use App\Entity\ArticleList;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ArticleListCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArticleList::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('suggestGame'),
            AssociationField::new('setupGame'),
            AssociationField::new('setupTchat'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_NEW);
    }
}
