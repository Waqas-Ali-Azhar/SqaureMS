<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script must be run from the command line.\n");
}

require_once dirname(__DIR__) . '/config/database.php';

function seedMissingLookupValues(mysqli $conn, string $table, array $values): void
{
    $columns = [
        'roles' => 'role',
        'entities' => 'entity_name',
        'brands' => 'brand',
        'plugin_type' => 'name',
    ];

    if (!isset($columns[$table])) {
        throw new InvalidArgumentException('Unsupported lookup table.');
    }

    $column = $columns[$table];
    $select = $conn->prepare("SELECT id FROM `{$table}` WHERE `{$column}` = ? LIMIT 1");
    $insert = $conn->prepare("INSERT INTO `{$table}` (`{$column}`) VALUES (?)");

    if (!$select || !$insert) {
        throw new RuntimeException('Could not prepare lookup seed statements.');
    }

    $conn->begin_transaction();

    try {
        foreach ($values as $value) {
            $select->bind_param('s', $value);
            $select->execute();
            $select->store_result();
            $exists = $select->num_rows > 0;
            $select->free_result();

            if ($exists) {
                fwrite(STDOUT, "Already exists: {$value}\n");
                continue;
            }

            $insert->bind_param('s', $value);
            $insert->execute();
            fwrite(STDOUT, "Added: {$value}\n");
        }

        $conn->commit();
    } catch (Throwable $exception) {
        $conn->rollback();
        throw $exception;
    } finally {
        $select->close();
        $insert->close();
    }
}

function seedMissingPlugins(mysqli $conn, array $plugins): void
{
    $select = $conn->prepare('SELECT id FROM plugins WHERE plugin_name = ? LIMIT 1');
    $insert = $conn->prepare('INSERT INTO plugins (plugin_name, plugin_type) VALUES (?, ?)');

    if (!$select || !$insert) {
        throw new RuntimeException('Could not prepare plugin seed statements.');
    }

    $conn->begin_transaction();

    try {
        foreach ($plugins as $plugin) {
            $name = $plugin['plugin_name'];
            $type = $plugin['plugin_type'];

            $select->bind_param('s', $name);
            $select->execute();
            $select->store_result();
            $exists = $select->num_rows > 0;
            $select->free_result();

            if ($exists) {
                fwrite(STDOUT, "Already exists: {$name}\n");
                continue;
            }

            $insert->bind_param('ss', $name, $type);
            $insert->execute();
            fwrite(STDOUT, "Added: {$name}\n");
        }

        $conn->commit();
    } catch (Throwable $exception) {
        $conn->rollback();
        throw $exception;
    } finally {
        $select->close();
        $insert->close();
    }
}
