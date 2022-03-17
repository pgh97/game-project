<?php
declare(strict_types=1);

namespace App\Domain\Common\Service;

use Predis\Client;

final class RedisService
{
    public const PROJECT_NAME = 'uruk-game';

    private Client $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    public function generateKey(string $value): string
    {
        return self::PROJECT_NAME . ':' . $value;
    }

    public function exists(string $key): int
    {
        return $this->redis->exists($key);
    }

    public function get(string $key): object
    {
        return json_decode((string) $this->redis->get($key));
    }

    public function set(string $key, object $value): void
    {
        $this->redis->set($key, json_encode($value));
    }

    public function setex(string $key, object $value, int $ttl = 3600): void
    {
        $this->redis->setex($key, $ttl, json_encode($value));
    }

    public function del(array $keys): void
    {
        $this->redis->del($keys);
    }
    //특정 ID 랭킹 추가 (누적)
    public function zincrby(string $key, string $id, int $score): void
    {
        $this->redis->zincrby($key, $score, $id);
    }
    //특정 ID 랭킹 추가 (교체)
    public function zadd(string $key, string $id, int $score): void
    {
        $this->redis->zadd($key, $score, $id);
    }
    //특정 ID 랭킹 삭제
    public function zrem(string $key, string $id): void
    {
        $this->redis->zrem($key, $id);
    }
    //특정 범위의 랭킹 조회하기 (점수 포함) 높은 점수가 1위
    public function zrevrange(string $key, int $startRank , int $endRank):array
    {
        return $this->redis->zrevrange($key, $startRank, $endRank, array('withscores' => true));
    }
    //특정 범위의 랭킹 조회하기 (점수 포함) 낮은 점수가 1위
    public function zrange(string $key, int $startRank , int $endRank): array
    {
        return $this->redis->zRange($key, $startRank, $endRank, array('withscores' => true));
    }
    //특정 ID 랭킹 점수 조회
    public function zscore(string $key, string $id): void
    {
        $this->redis->zscore($key, $id);
    }
    //특정 ID 랭킹 조회
    public function zrevrank(string $key, string $id): void
    {
        $this->redis->zrevrank($key, $id);
    }
}