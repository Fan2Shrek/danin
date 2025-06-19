<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as BaseCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCrudController extends BaseCrudController
{
    protected function getRequest(): Request
    {
        return $this->getContext()->getRequest();
    }

    protected function getLocaleField(): ChoiceField
    {
        return ChoiceField::new('locale')
                ->hideOnIndex()
                ->setRequired(true)
                ->setValue($this->getRequest()->getLocale())
                ->setChoices(array_combine(
                    $this->getParameter('kernel.enabled_locales'),
                    $this->getParameter('kernel.enabled_locales'),
                ))
            ->setFormTypeOption('data', $this->getRequest()->getLocale())
        ;
    }
}
