<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'jd_account', options: ['comment' => '京东账户表'])]
class Account implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '应用名称'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AppKey'])]
    private string $appKey;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AppSecret'])]
    private string $appSecret;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'AccessToken'])]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'RefreshToken'])]
    private ?string $refreshToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'AccessToken过期时间'])]
    private ?\DateTimeImmutable $accessTokenExpiresAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'RefreshToken过期时间'])]
    private ?\DateTimeImmutable $refreshTokenExpiresAt = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '授权码'])]
    private ?string $code = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '授权码过期时间'])]
    private ?\DateTimeImmutable $codeExpiresAt = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '授权状态'])]
    private ?string $state = null;

    use TimestampableAware;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAppKey(): string
    {
        return $this->appKey;
    }

    public function setAppKey(string $appKey): self
    {
        $this->appKey = $appKey;
        return $this;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    public function setAppSecret(string $appSecret): self
    {
        $this->appSecret = $appSecret;
        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getAccessTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->accessTokenExpiresAt;
    }

    public function setAccessTokenExpiresAt(?\DateTimeImmutable $accessTokenExpiresAt): self
    {
        $this->accessTokenExpiresAt = $accessTokenExpiresAt;
        return $this;
    }

    public function getRefreshTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->refreshTokenExpiresAt;
    }

    public function setRefreshTokenExpiresAt(?\DateTimeImmutable $refreshTokenExpiresAt): self
    {
        $this->refreshTokenExpiresAt = $refreshTokenExpiresAt;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getCodeExpiresAt(): ?\DateTimeImmutable
    {
        return $this->codeExpiresAt;
    }

    public function setCodeExpiresAt(?\DateTimeImmutable $codeExpiresAt): self
    {
        $this->codeExpiresAt = $codeExpiresAt;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function isAccessTokenExpired(): bool
    {
        if (null === $this->accessTokenExpiresAt) {
            return true;
        }

        return $this->accessTokenExpiresAt < new \DateTimeImmutable();
    }

    public function isRefreshTokenExpired(): bool
    {
        if (null === $this->refreshTokenExpiresAt) {
            return true;
        }

        return $this->refreshTokenExpiresAt < new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
