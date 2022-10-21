<?php use CMW\Controller\Core\MenusController;

?>

<header>
    <nav>
        <ul>
            <?php
            /** @var MenusController $menu */

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