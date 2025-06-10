<?php

namespace App\Controller\Admin;

use App\Entity\Command;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\HttpFoundation\Request;

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
            ChoiceField::new('locale')
                ->hideOnIndex()
                ->setRequired(true)
                ->setValue($this->getRequest()->getLocale())
                ->setChoices(array_combine(
                    $this->getParameter('kernel.enabled_locales'),
                    $this->getParameter('kernel.enabled_locales'),
                ))
                ->setFormTypeOption('data', $this->getRequest()->getLocale()),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_NEW);
    }

    private function getRequest(): Request
    {
        return $this->getContext()->getRequest();
    }
}
