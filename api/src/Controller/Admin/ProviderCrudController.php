<?php

namespace App\Controller\Admin;

use App\Entity\Provider;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProviderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Provider::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('command'),
            ImageField::new('img')
                ->setBasePath('/uploads/provider')
                ->setUploadDir('public/uploads/provider')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(true),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_NEW);
    }
}
