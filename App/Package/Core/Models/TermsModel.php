<?php

namespace CMW\Model\Core;

use CMW\Entity\Core\TermsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;
use CMW\Type\Core\Enum\TermType;
use DateTimeImmutable;
use DateTimeInterface;
use PDO;

/**
 * Class: @TermsModel
 * @package Core
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/models
 */
class TermsModel extends AbstractModel
{
    private function pdo(): \PDO
    {
        return DatabaseManager::getInstance();
    }

    private function hydrate(array $row): TermsEntity
    {
        $editor = null;
        if (!is_null($row['term_last_editor'])) {
            $editor = UsersModel::getInstance()->getUserById((int)$row['term_last_editor']);
        }

        return new TermsEntity(
            (int)$row['term_id'],
            TermType::from((string)$row['term_type']),
            (string)$row['term_content'],
            (int)$row['term_requires_accept'],
            (string)$row['term_published_at'],
            $editor
        );
    }

    /**
     * @param \CMW\Type\Core\Enum\TermType $type
     * @return \CMW\Entity\Core\TermsEntity|null
     */
    public function getLatestByType(TermType $type): ?TermsEntity
    {
        $stmt = $this->pdo()->prepare(
            'SELECT * FROM cmw_core_terms WHERE term_type = :type ORDER BY term_id DESC LIMIT 1'
        );
        if (!$stmt->execute(['type' => $type->value])) return null;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    /** @return TermsEntity[] */
    public function getLatestPerType(): array
    {
        $sql = "
            SELECT t.*
            FROM cmw_core_terms t
            INNER JOIN (
                SELECT term_type, MAX(term_id) AS last_id
                FROM cmw_core_terms
                GROUP BY term_type
            ) m ON m.term_type = t.term_type AND m.last_id = t.term_id
            ORDER BY t.term_type
        ";
        $stmt = $this->pdo()->prepare($sql);
        if (!$stmt->execute()) return [];

        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $this->hydrate($row);
        }
        return $out;
    }

    /** @return TermsEntity[] */
    public function listHistory(TermType $type, int $limit = 50, int $offset = 0): array
    {
        $sql = "
            SELECT *
            FROM cmw_core_terms
            WHERE term_type = :type
            ORDER BY term_id DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->bindValue(':type', $type->value, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        if (!$stmt->execute()) return [];

        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $this->hydrate($row);
        }
        return $out;
    }

    /**
     * Publie une nouvelle version (INSERT-only).
     * @return int|null  term_id inséré
     */
    public function publish(TermType $type, string $htmlContent, bool $requiresAccept, int $editorUserId): ?int
    {
        $stmt = $this->pdo()->prepare("
            INSERT INTO cmw_core_terms (term_type, term_content, term_requires_accept, term_published_at, term_last_editor)
            VALUES (:type, :content, :req, NOW(), :editor)
        ");

        $ok = $stmt->execute([
            'type'    => $type->value,
            'content' => $htmlContent,
            'req'     => $requiresAccept ? 1 : 0,
            'editor'  => $editorUserId,
        ]);

        return $ok ? (int)$this->pdo()->lastInsertId() : null;
    }

    /**
     * Dernière date de publication qui REQUIERT re-acceptation (cutoff global).
     */
    public function getLastRequiredCutoff(): DateTimeImmutable
    {
        $row = $this->pdo()->query(
            "SELECT MAX(term_published_at) AS last_update FROM cmw_core_terms WHERE term_requires_accept = 1"
        )?->fetch(PDO::FETCH_ASSOC);

        $ts = $row['last_update'] ?? '1970-01-01 00:00:00';
        return new DateTimeImmutable($ts);
    }

    /** @return TermType[] */
    public function getOutdatedTypesSince(DateTimeInterface $acceptedAt): array
    {
        $sql = "
            SELECT term_type, MAX(term_published_at) AS latest
            FROM cmw_core_terms
            WHERE term_requires_accept = 1
            GROUP BY term_type
            HAVING latest > :cutoff
        ";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute(['cutoff' => $acceptedAt->format('Y-m-d H:i:s')]);

        $types = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $types[] = TermType::from($row['term_type']);
        }
        return $types;
    }

    public function isTypeActive(TermType $type): bool
    {
        if ($type->isMandatory()) {
            return true;
        }

        $stmt = $this->pdo()->prepare(
            "SELECT is_active FROM cmw_core_terms_settings WHERE term_type = :t LIMIT 1"
        );
        $stmt->execute(['t' => $type->value]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        // défaut: actif si non configuré (change en false si tu veux)
        return $row ? (bool)$row['is_active'] : true;
    }

    public function setTypeActive(TermType $type, bool $active): void
    {
        if ($active === false && $type->isMandatory()) {
            throw new \RuntimeException('This term type is mandatory and cannot be disabled.');
        }

        $stmt = $this->pdo()->prepare("
        INSERT INTO cmw_core_terms_settings (term_type, is_active)
        VALUES (:t, :a)
        ON DUPLICATE KEY UPDATE is_active = VALUES(is_active)
    ");
        $stmt->execute(['t' => $type->value, 'a' => $active ? 1 : 0]);
    }

    /** @return TermsEntity[] actifs seulement */
    public function getLatestPerTypeActive(): array
    {
        $all = $this->getLatestPerType();
        $out = [];
        foreach ($all as $term) {
            if ($this->isTypeActive($term->getType())) {
                $out[] = $term;
            }
        }
        return $out;
    }

    /** @return TermType[] */
    public function getActiveTypes(): array
    {
        $types = [];
        foreach (TermType::cases() as $t) {
            if ($this->isTypeActive($t)) {
                $types[] = $t;
            }
        }
        return $types;
    }

}
