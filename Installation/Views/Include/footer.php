<?php

use CMW\Manager\Updater\UpdatesManager;

?>
</div>
</div>
</body>
<footer class="text-sm mt-auto xl:px-64">
    <div class="flex flex-wrap  items-center">
        <div class="px-6 py-6 md:flex-1">
            <p>Copyright © <a class="text-cmw-pink" href="https://craftmywebsite.fr/"
                              target="_blank">CraftMyWebsite</a> (☞ﾟヮﾟ)☞ <small><?= UpdatesManager::getVersion() ?></small></p>
        </div>
        <div class="px-4 py-2 w-full sm:w-auto">
            <div class="flex-wrap inline-flex space-x-3">
                <a href="https://craftmywebsite.fr/discord" target="_blank" class="hover:text-blue-600">
                    <i class="fa-xl fa-brands fa-discord"></i>
                </a>
                <a href="https://x.com/CraftMyWebsite" target="_blank" class="hover:text-blue-600">
                    <i class="fa-xl fa-brands fa-twitter"></i>
                </a>
            </div>
        </div>
    </div>
</footer>
</html>