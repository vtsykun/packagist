<?php declare(strict_types=1);

namespace Packagist\WebBundle\Entity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DownloadRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Download::class);
    }

    public function deletePackageDownloads(Package $package)
    {
        $conn = $this->getEntityManager()->getConnection();

        $conn->executeUpdate('DELETE FROM download WHERE package_id = :id', ['id' => $package->getId()]);
    }

    public function findDataByMajorVersion(Package $package, int $majorVersion)
    {
        $sql = '
            SELECT d.data
            FROM package_version v
            INNER JOIN download d ON d.id=v.id AND d.type = :versionType
            WHERE v.package_id = :package AND v.normalizedVersion LIKE :majorVersion
        ';

        $stmt = $this->getEntityManager()->getConnection()
            ->executeQuery(
                $sql,
                ['package' => $package->getId(), 'versionType' => Download::TYPE_VERSION, 'majorVersion' => $majorVersion . '.%']
            );
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return array_map(function (array $row) {
            return $row['data'] ? json_decode($row['data'], true) : [];
        }, $result);
    }
}
