<?php

namespace CMW\Controller\Core\Api\External;

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Notice\WarningManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Model\Core\ActivatedModel;

/**
 * Class: @CheckerController
 * @package Core
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class CheckerController extends AbstractController
{
    /**
     * @return void
     * @desc ⚠️⚠️ Remove this function disables the paid resource check.
     * @desc BUT the API still queries your domain, so if you no longer do this check, craftmywebsite.fr is aware that you removed it and sanctions could apply ⚠️⚠️
     */
    public function checkActivationAPI(): void
    {
        //TODO notifymanager !
        $rows = ActivatedModel::getInstance()->getActivations();
        if (!$rows) return;

        $acts = [];
        foreach ($rows as $r) {
            $acts[] = [
                'resId' => (int)$r['resource_id'],
                'activationKey' => EncryptManager::decrypt($r['resource_key']),
            ];
        }

        $res = PublicAPI::postData('market/resources/check', ['activations' => $acts]);

        if (empty($res) || empty($res['success']) || empty($res['results']) || !is_array($res['results'])) {
            return;
        }

        foreach ($res['results'] as $r) {
            $type = (int)($r['type'] ?? 0);
            $name = $r['name'] ?? null;
            if (!$name) continue;

            if (in_array($r['status'], ['invalid_key','unlinked_domain'], true)) {
                $this->notifyFor($r);
            }

            if ($r['status'] === 'ok') {
                $this->removeDisabled($type, $name);
                continue;
            }

            $daysLeft = (int)($r['days_left'] ?? 7);
            $shouldDisable = (int)($r['should_disable'] ?? 0);

            if ($shouldDisable === 1 || $daysLeft <= 0) {
                $this->addDisabled($type, $name);
            }
        }
    }

    private function getCsvEnv(string $key): array {
        $val = EnvManager::getInstance()->getValue($key) ?? '';
        $val = trim($val);
        if ($val === '') {
            return [];
        }
        return array_values(array_unique(array_filter(array_map('trim', explode(',', $val)))));
    }

    private function setCsvEnv(string $key, array $values): void {
        $values = array_values(array_unique(array_filter(array_map('trim', $values))));
        EnvManager::getInstance()->setOrEditValue($key, $values ? implode(',', $values) : '');
    }

    private function addDisabled(int $type, string $name): void {
        $key = $type === 1 ? 'DISABLED_PACKAGE' : 'DISABLED_THEME';
        $list = $this->getCsvEnv($key);
        if (!in_array($name, $list, true)) {
            $list[] = $name;
            $this->setCsvEnv($key, $list);
        }
    }

    private function removeDisabled(int $type, string $name): void {
        $key = $type === 1 ? 'DISABLED_PACKAGE' : 'DISABLED_THEME';
        $list = $this->getCsvEnv($key);
        $list = array_values(array_filter($list, static fn($n) => $n !== $name));
        $this->setCsvEnv($key, $list);
    }

    private function notifyFor(array $r): void
    {
        // status-specific message
        if ($r['status'] === 'unlinked_domain') {
            error_log("[CMW] API CHECKER RUN '{$r['market_name']}' BUT API CMW NOT ALLOW THIS ON THIS DOMAIN (GRACE PERIOD) : {$_SERVER['HTTP_HOST']}, SUPPORT D'ONT HELP THIS USER AND NOTIFY ADMIN QUICKLY !");
            if ($r['days_left'] !== 0) {
                WarningManager::addInfo(
                    "CraftMyWebsite.fr a détecté que le domaine <b>{$_SERVER['HTTP_HOST']}</b> n’est pas lié à votre clé d'activation pour <b>{$r['market_name']}</b>. <br>".
                    "<a href=\"https://craftmywebsite.fr/market/manage/purchases\" class='link' target=\"_blank\" rel=\"noopener\">Ajoute le domaine</a> pour corriger le problème.<br>".
                    "Délai restant : <b>{$r['days_left']}</b> jour(s)."
                );
            }
        } elseif ($r['status'] === 'invalid_key') {
            error_log("[CMW] API CHECKER RUN '{$r['market_name']}' BUT API CMW NOT ALLOW THIS BECAUSE KEY NOT EXIST (GRACE PERIOD) : {$_SERVER['HTTP_HOST']}, SUPPORT D'ONT HELP THIS USER AND NOTIFY ADMIN QUICKLY !");
            if ($r['days_left'] !== 0) {
                WarningManager::addInfo(
                    "CraftMyWebsite.fr a détecté une clé d’activation invalide pour <b>{$r['market_name']}</b>. <br>" .
                    "Aucune correction possible, la clé n'existe plus chez CraftMyWebSite.fr (certainement une suppression de votre compte) La ressource sera désactivée dans {$r['days_left']} jour(s)."
                );
            }
        }
    }
}
