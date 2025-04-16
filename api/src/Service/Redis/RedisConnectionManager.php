<?php

declare(strict_types=1);

namespace App\Service\Redis;

final class RedisConnectionManager
{
    private ?\Redis $redis = null;

    public function __construct(
        private string $redisUrl,
    ) {
    }

    public function connection(): \Redis
    {
        if (null === $this->redis) {
            $infos = parse_url($this->redisUrl);
            $this->redis = new \Redis();
            $this->redis->connect($infos['host'], $infos['port'] ?? 6379);
        }

        return $this->redis;
    }

    public function close(): void
    {
        if (null !== $this->redis) {
            $this->redis->close();
            unset($this->redis);
        }
    }

    public function publish(string $channel, string $message): void
    {
        $this->connection()->publish($channel, $message);
    }

    public function subscribe(array $channels, callable $callback): void
    {
        $this->connection();
        $this->redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
        $this->connection()->subscribe($channels, $callback);
    }
}
