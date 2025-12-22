package redis

import (
	"context"
	"fmt"
	"log"
	"os"

	"github.com/redis/go-redis/v9"
)

type RedisClient struct {
	Client *redis.Client
}

func NewRedisClient() (*RedisClient, error) {
	addr := fmt.Sprintf("%s:%s", os.Getenv("REDIS_HOST"), os.Getenv("REDIS_PORT"))
	
	client := redis.NewClient(&redis.Options{
		Addr:     addr,
		Password: os.Getenv("REDIS_PASSWORD"),
		DB:       0,
	})

	ctx := context.Background()
	if err := client.Ping(ctx).Err(); err != nil {
		return nil, fmt.Errorf("failed to connect to Redis: %w", err)
	}

	log.Println("✅ Redis connected successfully")

	return &RedisClient{Client: client}, nil
}

func (r *RedisClient) PublishMessage(ctx context.Context, channel string, message interface{}) error {
	return r.Client.Publish(ctx, channel, message).Err()
}

func (r *RedisClient) Subscribe(ctx context.Context, channels ...string) *redis.PubSub {
	return r.Client.Subscribe(ctx, channels...)
}

func (r *RedisClient) SetUserOnline(ctx context.Context, userID int64) error {
	key := fmt.Sprintf("user:online:%d", userID)
	return r.Client.Set(ctx, key, "1", 0).Err()
}

func (r *RedisClient) SetUserOffline(ctx context.Context, userID int64) error {
	key := fmt.Sprintf("user:online:%d", userID)
	return r.Client.Del(ctx, key).Err()
}

func (r *RedisClient) IsUserOnline(ctx context.Context, userID int64) (bool, error) {
	key := fmt.Sprintf("user:online:%d", userID)
	val, err := r.Client.Get(ctx, key).Result()
	if err == redis.Nil {
		return false, nil
	}
	if err != nil {
		return false, err
	}
	return val == "1", nil
}

func (r *RedisClient) Close() error {
	return r.Client.Close()
}
