<?php

const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'bookandboard';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    return $pdo;
}

function db_available(): bool
{
    static $available = null;

    if ($available === null) {
        try {
            db();
            $available = true;
        } catch (PDOException $e) {
            $available = false;
        }
    }

    return $available;
}

const OFFER_UPLOADS = __DIR__ . '/../assets/img/offers';

const OFFER_TYPES = [
    'Travel Plan',
    'Travel and Hotel Package',
    'Complete Holiday Package',
];

function offer_row(array $row): array
{
    $row['id']     = (int) $row['id'];
    $row['price']  = (float) $row['price'];
    $row['active']     = (bool) $row['active'];
    $row['bestseller'] = (bool) $row['bestseller'];

    return $row;
}

function offers_select(string $conditions = '', array $params = []): array
{
    if (!db_available()) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT id, title, destination, type, description, price,
                start_date AS startDate, end_date AS endDate,
                image, alt, active, bestseller
           FROM offers ' . $conditions
    );
    $statement->execute($params);

    return array_map('offer_row', $statement->fetchAll());
}

const OFFER_NEWEST = 'ORDER BY created_at DESC, id DESC';

function offers_all(): array
{
    return offers_select(OFFER_NEWEST);
}

function offers_active(): array
{
    return offers_select('WHERE active = 1 ' . OFFER_NEWEST);
}

function offers_bestselling(int $limit = 3): array
{
    return offers_select('WHERE active = 1 AND bestseller = 1 ' . OFFER_NEWEST . ' LIMIT ' . max(1, $limit));
}

function offers_find(int $id): ?array
{
    return offers_select('WHERE id = ? LIMIT 1', [$id])[0] ?? null;
}

function offers_put(array $offer): int
{
    $id = (int) ($offer['id'] ?? 0);

    $values = [
        $offer['title'],
        $offer['destination'],
        $offer['type'],
        $offer['description'],
        $offer['price'],
        $offer['startDate'],
        $offer['endDate'],
        $offer['image'],
        $offer['alt'],
        empty($offer['active']) ? 0 : 1,
        empty($offer['bestseller']) ? 0 : 1,
    ];

    if ($id === 0) {
        $statement = db()->prepare(
            'INSERT INTO offers
                 (title, destination, type, description, price,
                  start_date, end_date, image, alt, active, bestseller)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute($values);

        return (int) db()->lastInsertId();
    }

    $statement = db()->prepare(
        'UPDATE offers
            SET title = ?, destination = ?, type = ?, description = ?, price = ?,
                start_date = ?, end_date = ?, image = ?, alt = ?, active = ?,
                bestseller = ?
          WHERE id = ?'
    );
    $statement->execute([...$values, $id]);

    return $id;
}

function offers_delete(int $id): bool
{
    $statement = db()->prepare('DELETE FROM offers WHERE id = ?');
    $statement->execute([$id]);

    return $statement->rowCount() > 0;
}

function offers_set_active(int $id, bool $active): bool
{

    if (!offers_find($id)) {
        return false;
    }

    $statement = db()->prepare('UPDATE offers SET active = ? WHERE id = ?');
    $statement->execute([$active ? 1 : 0, $id]);

    return true;
}

function branch_row(array $row): array
{
    $row['id']           = (int) $row['id'];
    $row['isHeadOffice'] = (bool) $row['isHeadOffice'];

    return $row;
}

function branches_select(string $conditions, array $params = []): array
{
    if (!db_available()) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT id, name, location, street, area, phone, tel, email, hours,
                is_head_office AS isHeadOffice
           FROM branches ' . $conditions
    );
    $statement->execute($params);

    return array_map('branch_row', $statement->fetchAll());
}

function branches_all(): array
{
    return branches_select('ORDER BY id');
}

function branches_public(): array
{
    return branches_select('WHERE is_head_office = 0 ORDER BY id');
}

function branch_head_office(): ?array
{
    return branches_select('WHERE is_head_office = 1 ORDER BY id LIMIT 1')[0] ?? null;
}

function branches_find(int $id): ?array
{
    return branches_select('WHERE id = ? LIMIT 1', [$id])[0] ?? null;
}

function branches_put(array $branch): int
{
    $id = (int) ($branch['id'] ?? 0);

    $values = [
        $branch['name'],
        $branch['location'],
        $branch['street'],
        $branch['area'],
        $branch['phone'],
        $branch['tel'],
        $branch['email'],
        $branch['hours'],
        empty($branch['isHeadOffice']) ? 0 : 1,
    ];

    if ($id === 0) {
        $statement = db()->prepare(
            'INSERT INTO branches
                 (name, location, street, area, phone, tel, email, hours, is_head_office)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute($values);

        return (int) db()->lastInsertId();
    }

    $statement = db()->prepare(
        'UPDATE branches
            SET name = ?, location = ?, street = ?, area = ?,
                phone = ?, tel = ?, email = ?, hours = ?, is_head_office = ?
          WHERE id = ?'
    );
    $statement->execute([...$values, $id]);

    return $id;
}

function branches_delete(int $id): bool
{
    $statement = db()->prepare('DELETE FROM branches WHERE id = ?');
    $statement->execute([$id]);

    return $statement->rowCount() > 0;
}

function branches_clear_head_office(int $exceptId): void
{
    $statement = db()->prepare('UPDATE branches SET is_head_office = 0 WHERE id <> ?');
    $statement->execute([$exceptId]);
}

function flights_destinations(): array
{
    if (!db_available()) {
        return [];
    }

    return db()->query('SELECT DISTINCT destination FROM flights ORDER BY destination')
        ->fetchAll(PDO::FETCH_COLUMN);
}

function flights_origins(): array
{
    if (!db_available()) {
        return [];
    }

    return db()->query('SELECT DISTINCT origin FROM flights ORDER BY origin')
        ->fetchAll(PDO::FETCH_COLUMN);
}

function flights_search(array $criteria): array
{
    if (!db_available()) {
        return [];
    }

    $where  = [];
    $params = [];

    $destination = trim((string) ($criteria['destination'] ?? ''));

    if ($destination !== '') {
        $where[]  = 'destination = ?';
        $params[] = $destination;
    }

    $dateFrom = trim((string) ($criteria['dateFrom'] ?? ''));
    $dateTo   = trim((string) ($criteria['dateTo'] ?? ''));

    if ($dateFrom !== '') {
        $where[]  = 'departure_date BETWEEN ? AND ?';
        $params[] = $dateFrom;
        $params[] = $dateTo ?: $dateFrom;
    } elseif ($dateTo !== '') {
        $where[]  = 'departure_date <= ?';
        $params[] = $dateTo;
    }

    $origin = trim((string) ($criteria['origin'] ?? ''));

    if ($origin !== '') {
        $where[]  = 'origin = ?';
        $params[] = $origin;
    }

    $maxPrice = (float) ($criteria['maxPrice'] ?? 0);

    if ($maxPrice > 0) {
        $where[]  = 'price <= ?';
        $params[] = $maxPrice;
    }

    $maxDuration = (int) ($criteria['maxDuration'] ?? 0);

    if ($maxDuration > 0) {
        $where[]  = 'duration_minutes <= ?';
        $params[] = $maxDuration;
    }

    $stops = array_values(array_unique(array_map('intval', (array) ($criteria['stops'] ?? []))));

    if ($stops) {
        $clauses = [];

        foreach ($stops as $count) {
            if ($count >= 2) {
                $clauses[] = 'stops >= 2';
            } else {
                $clauses[] = 'stops = ?';
                $params[]  = $count;
            }
        }

        $where[] = '(' . implode(' OR ', $clauses) . ')';
    }

    $statement = db()->prepare(
        'SELECT airline, origin, destination,
                departure_date   AS departureDate,
                departure_time   AS departureTime,
                arrival_time     AS arrivalTime,
                duration_minutes AS durationMinutes,
                stops, price
           FROM flights'
        . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . '
          ORDER BY departure_date, price, departure_time'
    );
    $statement->execute($params);

    $rows = $statement->fetchAll();

    foreach ($rows as &$row) {
        $row['durationMinutes'] = (int) $row['durationMinutes'];
        $row['stops']           = (int) $row['stops'];
        $row['price']           = (float) $row['price'];
    }

    return $rows;
}

function hotels_destinations(): array
{
    if (!db_available()) {
        return [];
    }

    return db()->query('SELECT DISTINCT destination FROM hotels ORDER BY destination')
        ->fetchAll(PDO::FETCH_COLUMN);
}

function hotels_search(array $criteria): array
{
    if (!db_available()) {
        return [];
    }

    $where  = [];
    $params = [];

    $destination = trim((string) ($criteria['destination'] ?? ''));

    if ($destination !== '') {
        $where[]  = 'destination = ?';
        $params[] = $destination;
    }

    $checkIn  = trim((string) ($criteria['checkIn'] ?? ''));
    $checkOut = trim((string) ($criteria['checkOut'] ?? ''));

    if ($checkIn !== '' && $checkOut !== '') {
        $where[]  = 'available_from <= ?';
        $params[] = $checkIn;
        $where[]  = 'available_to >= ?';
        $params[] = $checkOut;
    }

    $maxPrice = (float) ($criteria['maxPrice'] ?? 0);

    if ($maxPrice > 0) {
        $where[]  = 'price_per_night <= ?';
        $params[] = $maxPrice;
    }

    $statement = db()->prepare(
        'SELECT name, destination, description,
                available_from  AS availableFrom,
                available_to    AS availableTo,
                price_per_night AS pricePerNight,
                rating, image, alt
           FROM hotels'
        . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . '
          ORDER BY price_per_night, name'
    );
    $statement->execute($params);

    $rows = $statement->fetchAll();

    foreach ($rows as &$row) {
        $row['pricePerNight'] = (float) $row['pricePerNight'];
        $row['rating']        = (float) $row['rating'];
    }

    return $rows;
}

function packages_for_customer(int $customerId): array
{
    if (!db_available()) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT title, destination,
                start_date   AS startDate,
                end_date     AS endDate,
                package_type AS packageType,
                status
           FROM previous_packages
          WHERE customer_id = ?
          ORDER BY start_date DESC'
    );
    $statement->execute([$customerId]);

    return $statement->fetchAll();
}
