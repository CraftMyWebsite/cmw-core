<?php use CMW\Controller\Menus\menusController;
use CMW\Controller\coreController; ?>

<header>
    <nav>
        <ul>
            <?php
            /** @var menusController $menu */

            $menu = $menu->cmwMenu();
            foreach ($menu as $item) :
                echo "<li><a href='$item->menu_url'>$item->menu_name</a></li>";
            endforeach; ?>
        </ul>
    </nav>
</header>