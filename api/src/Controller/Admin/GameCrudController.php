<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Enum\GameEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('id')->setChoices(GameEnum::cases())
                ->hideOnIndex()
                ->hideOnForm(),
            TextField::new('name'),
            TextareaField::new('description'),
            ImageField::new('img')
                ->setBasePath('/uploads/game')
                ->setUploadDir('public/uploads/game')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(true),
            $this->getLocaleField(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_NEW);
    }
}
