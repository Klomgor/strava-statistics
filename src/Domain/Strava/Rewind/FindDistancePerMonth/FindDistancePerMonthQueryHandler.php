<?php

declare(strict_types=1);

namespace App\Domain\Strava\Rewind\FindDistancePerMonth;

use App\Domain\Strava\Activity\SportType\SportType;
use App\Domain\Strava\Calendar\Month;
use App\Infrastructure\CQRS\Query\Query;
use App\Infrastructure\CQRS\Query\QueryHandler;
use App\Infrastructure\CQRS\Query\Response;
use App\Infrastructure\ValueObject\Measurement\Length\Meter;
use App\Infrastructure\ValueObject\Time\SerializableDateTime;
use Doctrine\DBAL\Connection;

final readonly class FindDistancePerMonthQueryHandler implements QueryHandler
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function handle(Query $query): Response
    {
        assert($query instanceof FindDistancePerMonth);

        $totalDistance = (int) $this->connection->executeQuery(
            <<<SQL
                SELECT SUM(distance) as distance
                FROM Activity
                WHERE strftime('%Y',startDateTime) = :year
            SQL,
            [
                'year' => (string) $query->getYear(),
            ]
        )->fetchOne();

        $results = $this->connection->executeQuery(
            <<<SQL
                SELECT strftime('%Y-%m', startDateTime) AS yearAndMonth, sportType, SUM(distance) as distance
                FROM Activity
                WHERE strftime('%Y',startDateTime) = :year
                GROUP BY sportType, yearAndMonth
                ORDER BY sportType ASC, yearAndMonth ASC
            SQL,
            [
                'year' => (string) $query->getYear(),
            ]
        )->fetchAllAssociative();

        return new FindDistancePerMonthResponse(
            distancePerMonth: array_map(
                fn (array $result) => [
                    Month::fromDate(SerializableDateTime::fromString(sprintf('%s-01', $result['yearAndMonth']))),
                    SportType::from($result['sportType']),
                    Meter::from($result['distance'])->toKilometer(),
                ],
                $results,
            ),
            totalDistance: Meter::from($totalDistance)->toKilometer()
        );
    }
}
