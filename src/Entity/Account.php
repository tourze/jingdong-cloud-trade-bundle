<?php

namespace JingdongCloudTradeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'jd_account', options: ['comment' => '京东账户表'])]
class Account implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '应用名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AppKey'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $appKey;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AppSecret'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $appSecret;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'AccessToken'])]
    #[Assert\Length(max: 255)]
    private ?string $accessToken = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'RefreshToken'])]
    #[Assert\Length(max: 255)]
    private ?string $refreshToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'AccessToken过期时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $accessTokenExpireTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'RefreshToken过期时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $refreshTokenExpireTime = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '授权码'])]
    #[Assert\Length(max: 255)]
    private ?string $code = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '授权码过期时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $codeExpireTime = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '授权状态'])]
    #[Assert\Length(max: 255)]
    private ?string $state = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAppKey(): string
    {
        return $this->appKey;
    }

    public function setAppKey(string $appKey): void
    {
        $this->appKey = $appKey;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getAccessTokenExpireTime(): ?\DateTimeImmutable
    {
        return $this->accessTokenExpireTime;
    }

    public function setAccessTokenExpireTime(?\DateTimeImmutable $accessTokenExpireTime): void
    {
        $this->accessTokenExpireTime = $accessTokenExpireTime;
    }

    public function getRefreshTokenExpireTime(): ?\DateTimeImmutable
    {
        return $this->refreshTokenExpireTime;
    }

    public function setRefreshTokenExpireTime(?\DateTimeImmutable $refreshTokenExpireTime): void
    {
        $this->refreshTokenExpireTime = $refreshTokenExpireTime;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getCodeExpireTime(): ?\DateTimeImmutable
    {
        return $this->codeExpireTime;
    }

    public function setCodeExpireTime(?\DateTimeImmutable $codeExpireTime): void
    {
        $this->codeExpireTime = $codeExpireTime;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function isAccessTokenExpired(): bool
    {
        if (null === $this->accessTokenExpireTime) {
            return true;
        }

        return $this->accessTokenExpireTime < new \DateTimeImmutable();
    }

    public function isRefreshTokenExpired(): bool
    {
        if (null === $this->refreshTokenExpireTime) {
            return true;
        }

        return $this->refreshTokenExpireTime < new \DateTimeImmutable();
    }

    public function getAccessTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->getAccessTokenExpireTime();
    }

    public function setAccessTokenExpiresAt(?\DateTimeImmutable $accessTokenExpiresAt): void
    {
        $this->setAccessTokenExpireTime($accessTokenExpiresAt);
    }

    public function getRefreshTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->getRefreshTokenExpireTime();
    }

    public function setRefreshTokenExpiresAt(?\DateTimeImmutable $refreshTokenExpiresAt): void
    {
        $this->setRefreshTokenExpireTime($refreshTokenExpiresAt);
    }

    public function getCodeExpiresAt(): ?\DateTimeImmutable
    {
        return $this->getCodeExpireTime();
    }

    public function setCodeExpiresAt(?\DateTimeImmutable $codeExpiresAt): void
    {
        $this->setCodeExpireTime($codeExpiresAt);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
