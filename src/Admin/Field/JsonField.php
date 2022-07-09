<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class JsonField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)

            // this template is used in 'index' and 'detail' pages
            ->setTemplatePath('admin/field/json.html.twig')

            // these methods allow to define the web assets loaded when the
            // field is displayed in any CRUD page (index/detail/edit/new)
//            ->addCssFiles('js/admin/field-map.css')
//            ->addJsFiles('js/admin/field-map.js')
            ;
    }
}
