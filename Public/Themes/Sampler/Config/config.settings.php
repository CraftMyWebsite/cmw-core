<?php
use CMW\Manager\Theme\Editor\Entities\EditorMenu;
use CMW\Manager\Theme\Editor\Entities\EditorRangeOptions;
use CMW\Manager\Theme\Editor\Entities\EditorSelectOptions;
use CMW\Manager\Theme\Editor\Entities\EditorType;
use CMW\Manager\Theme\Editor\Entities\EditorValue;

return [
    new EditorMenu(
        title: 'Globaux',
        key: 'global',
        scope: null,
        requiredPackage: null,
        values: [
            new EditorValue(
                title: 'Police d\'écriture',
                themeKey: 'main_font',
                defaultValue: 'font-exo2',
                type: EditorType::SELECT,
                selectOptions: [
                    new EditorSelectOptions(value: 'font-angkor', text: 'Angkor'),
                    new EditorSelectOptions(value: 'font-ibmplexsans', text: 'ibmplexsans'),
                    new EditorSelectOptions(value: 'font-kanit', text: 'kanit'),
                    new EditorSelectOptions(value: 'font-lora', text: 'lora'),
                    new EditorSelectOptions(value: 'font-madimione', text: 'madimione'),
                    new EditorSelectOptions(value: 'font-ojuju', text: 'ojuju'),
                    new EditorSelectOptions(value: 'font-opensans', text: 'opensans'),
                    new EditorSelectOptions(value: 'font-playfairdisplay', text: 'playfairdisplay'),
                    new EditorSelectOptions(value: 'font-robotocondensed', text: 'robotocondensed'),
                    new EditorSelectOptions(value: 'font-robotomono', text: 'robotomono'),
                    new EditorSelectOptions(value: 'font-robotoslab', text: 'robotoslab'),
                    new EditorSelectOptions(value: 'font-rubik', text: 'rubik'),
                    new EditorSelectOptions(value: 'font-ubuntu', text: 'ubuntu'),
                    new EditorSelectOptions(value: 'font-roboto', text: 'roboto'),
                    new EditorSelectOptions(value: 'font-unbounded', text: 'unbounded'),
                    new EditorSelectOptions(value: 'font-montserrat', text: 'montserrat'),
                    new EditorSelectOptions(value: 'font-paytone', text: 'paytone'),
                    new EditorSelectOptions(value: 'font-sora', text: 'sora'),
                    new EditorSelectOptions(value: 'font-outfit', text: 'outfit'),
                    new EditorSelectOptions(value: 'font-alata', text: 'alata'),
                    new EditorSelectOptions(value: 'font-titan', text: 'titan'),
                    new EditorSelectOptions(value: 'font-pressstart', text: 'pressstart'),
                    new EditorSelectOptions(value: 'font-abrilfatface', text: 'abrilfatface'),
                    new EditorSelectOptions(value: 'font-afacadflux', text: 'afacadflux'),
                    new EditorSelectOptions(value: 'font-amaticsc', text: 'amaticsc'),
                    new EditorSelectOptions(value: 'font-archivo', text: 'archivo'),
                    new EditorSelectOptions(value: 'font-cabin', text: 'cabin'),
                    new EditorSelectOptions(value: 'font-caveat', text: 'caveat'),
                    new EditorSelectOptions(value: 'font-concretone', text: 'concretone'),
                    new EditorSelectOptions(value: 'font-crimsonpro', text: 'crimsonpro'),
                    new EditorSelectOptions(value: 'font-exo2', text: 'exo2'),
                    new EditorSelectOptions(value: 'font-lato', text: 'lato'),
                    new EditorSelectOptions(value: 'font-lobster', text: 'lobster'),
                    new EditorSelectOptions(value: 'font-marcellus', text: 'marcellus'),
                    new EditorSelectOptions(value: 'font-merriweather', text: 'merriweather'),
                    new EditorSelectOptions(value: 'font-noto', text: 'noto'),
                    new EditorSelectOptions(value: 'font-oleo', text: 'oleo'),
                    new EditorSelectOptions(value: 'font-playwriteausa', text: 'playwriteausa'),
                    new EditorSelectOptions(value: 'font-playwrite', text: 'playwrite'),
                    new EditorSelectOptions(value: 'font-pt', text: 'pt'),
                    new EditorSelectOptions(value: 'font-quicksand', text: 'quicksand'),
                    new EditorSelectOptions(value: 'font-satisfy', text: 'satisfy'),
                    new EditorSelectOptions(value: 'font-silkscreen', text: 'silkscreen'),
                ]
            ),
            new EditorValue(
                title: 'Couleur du fond',
                themeKey: 'bg-color',
                defaultValue: '#1b1f23',
                type: EditorType::COLOR,
            ),
            new EditorValue(
                title: 'Couleur du fond secondaire',
                themeKey: 'bg-color-secondary',
                defaultValue: '#14171a',
                type: EditorType::COLOR,
            ),
        ]
    ),
    new EditorMenu(
        title: 'En tête',
        key: 'header',
        scope: null,
        requiredPackage: null,
        values: [
            new EditorValue(
                title: 'Afficher les titre',
                themeKey: 'header_active_title',
                defaultValue: '1',
                type: EditorType::BOOLEAN,
            ),
            new EditorValue(
                title: 'Afficher le logo',
                themeKey: 'header_active_logo',
                defaultValue: '1',
                type: EditorType::BOOLEAN,
            ),
            new EditorValue(
                title: 'Logo',
                themeKey: 'site_image',
                defaultValue: 'Config/Default/Img/logo-sampler.png',
                type: EditorType::IMAGE
            ),
            new EditorValue(
                title: 'Taille du logo',
                themeKey: 'site_image_width',
                defaultValue: '32',
                type: EditorType::RANGE,
                rangeOptions: [
                    new EditorRangeOptions(min: 0, max: 256,step: 1,suffix: 'px')
                ]
            ),
            new EditorValue(
                title: 'Couleur du bandeau',
                themeKey: 'bg-bandeau',
                defaultValue: '#14171a',
                type: EditorType::COLOR,
            ),
        ]
    ),
    new EditorMenu(
        title: 'Accueil - Hero',
        key: 'home-hero',
        scope: null,
        requiredPackage: null,
        values: [
            new EditorValue(
                title: 'Logo',
                themeKey: 'image',
                defaultValue: 'Config/Default/Img/hero.png',
                type: EditorType::IMAGE
            ),
            new EditorValue(
                title: 'Taille du logo',
                themeKey: 'image_width',
                defaultValue: '380',
                type: EditorType::RANGE,
                rangeOptions: [
                    new EditorRangeOptions(min: 0, max: 1024,step: 1,suffix: 'px')
                ]
            ),
        ]
    ),
];