<?php
use ZfcTwitterBootstrap\Form\View;
use ZfcTwitterBootstrap\Navigation;
use ZfcTwitterBootstrap\View\Helper;


return [
    'view_helpers' => [
        'aliases'    => [
            'ztbnavigation' => Helper\Navigation::class,
        ],
        'delegators' => [],
        'factories'  => [
            Helper\Navigation::class  => Navigation\View\NavigationHelperFactory::class,
            'ztbviewhelpernavigation' => Navigation\View\NavigationHelperFactory::class,
        ],
        'invokables' => [
            'ztbalert'           => Helper\Alert::class,
            'ztbbadge'           => Helper\Badge::class,
            'ztbcloseicon'       => Helper\CloseIcon::class,
            'ztbflashmessenger'  => Helper\FlashMessenger::class,
            'ztbform'            => View\Helper\Form::class,
            'ztbformdescription' => View\Helper\FormDescription::class,
            'ztbformelement'     => View\Helper\FormElement::class,
            'ztbformrenderer'    => View\Helper\Form::class,
            'ztbicon'            => Helper\Icon::class,
            'ztbimage'           => Helper\Image::class,
            'ztbpanel'           => Helper\Panel::class,
            'ztblabel'           => Helper\Label::class,
            'ztbwell'            => Helper\Well::class,
        ],
    ],
];
