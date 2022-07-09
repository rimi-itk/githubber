<?php

namespace App\Controller\Admin;

use App\Entity\Organization;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrganizationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Organization::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield BooleanField::new('isUser')
            ->renderAsSwitch(false);
        yield AssociationField::new('repositories')
            ->hideOnForm();
        yield DateTimeField::new('createdAt')
            ->hideOnForm();
    }
}
