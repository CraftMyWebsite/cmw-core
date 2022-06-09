<?php use CMW\Controller\Menus\menusController;

?>

<header>
    <nav>
        <ul>
            <?php
            /** @var menusController $menu */

            $menu = $menu->cmwMenu();
            foreach ($menu as $item) :
                echo <<<HTML
                    <li>
                        <a href='$item->menu_url'>$item->menu_name</a>
                    </li>
                HTML;
            endforeach; ?>
        </ul>
    </nav>
</header>