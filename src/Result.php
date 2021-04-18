<?php declare(strict_types=1);

/*
 * This file is part of the VV package.
 *
 * (c) Volodymyr Sarnytskyi <v00v4n@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace VV\Db\Oci;

/**
 * Class Result
 *
 * @package VV\Db\Oci
 */
class Result implements \VV\Db\Driver\Result {

    private mixed $stmt;
    private int|string|null $insertedId;

    public function __construct(mixed $stmt, int|string|null $insertedId) {
        $this->stmt = $stmt;
        $this->insertedId = $insertedId;
    }

    public function fetchIterator(int $flags): \Traversable {
        $ociFlags = OCI_RETURN_NULLS;

        if ($fassoc = (bool)($flags & \VV\Db::FETCH_ASSOC)) {
            $ociFlags |= OCI_ASSOC;
        }
        if ($flags & \VV\Db::FETCH_NUM) {
            $ociFlags |= OCI_NUM;
        }
        if (!($flags & \VV\Db::FETCH_LOB_NOT_LOAD)) {
            $ociFlags |= OCI_RETURN_LOBS;
        }

        while ($row = oci_fetch_array($this->stmt, $ociFlags)) {
            if ($fassoc) $row = array_change_key_case($row, CASE_LOWER);
            yield $row;
        }
    }

    public function insertedId(): int|string|null {
        return $this->insertedId;
    }

    public function affectedRows(): int {
        return oci_num_rows($this->stmt);
    }

    /**
     * Closes statement
     */
    public function close(): void { }
}
