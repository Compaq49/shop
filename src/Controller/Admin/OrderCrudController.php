<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    public function configureActions(Actions $actions): Actions 
    {
	return $actions
		->add('index', 'detail')   // on ajoute le lien voir
                	->disable(Action::EDIT, Action::DELETE); //on supprime les liens edit et supprimer
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', "Id"),    
            DateField::new('createdAt', "Passée le"),
            TextField::new('user.getFullName', 'Utilisateur'),
            MoneyField::new('total')->setCurrency('EUR'),
            //TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total', 'Total commande')->setCurrency('EUR'),
            //TextField::new('carrierName', 'Transporteur'),
        ];
    }
    
}
