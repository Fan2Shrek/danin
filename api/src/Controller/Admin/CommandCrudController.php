<?php

namespace App\Controller\Admin;

use App\Entity\Command;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class CommandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Command::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextareaField::new('description'),
            $this->getLocaleField(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_NEW);
    }
}
