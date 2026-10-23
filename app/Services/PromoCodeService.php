<?php

namespace App\Services;

use App\Models\Promocode;
use Illuminate\Database\QueryException;

class PromoCodeService
{
    protected const UPPER  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected const LOWER  = 'abcdefghijklmnopqrstuvwxyz';
    protected const DIGITS = '0123456789';

    protected function pickOne(string $set): string
    {
        return $set[random_int(0, strlen($set) - 1)];
    }

    protected function pickMany(string $set, int $count): array
    {
        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $out[] = $this->pickOne($set);
        }
        return $out;
    }

    protected function secureShuffle(array &$arr): void
    {
        for ($i = count($arr) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
        }
    }

    protected function buildComposedCode(): string
    {
        $chars = array_merge(
            $this->pickMany(static::UPPER, 2),
            $this->pickMany(static::LOWER, 2),
            $this->pickMany(static::DIGITS, 2),
        );

        $pools = [static::UPPER, static::LOWER, static::DIGITS];
        for ($i = 0; $i < 2; $i++) {
            $pool = $pools[random_int(0, count($pools) - 1)];
            $chars[] = $this->pickOne($pool);
        }

        $this->secureShuffle($chars);
        return implode('', $chars);
    }

    public function generateCode(): string
    {
        return $this->buildComposedCode();
    }


    public function create(array $data, int $tries = 5): Promocode
    {
        for ($i = 0; $i < $tries; $i++) {
            try {
                $data['code'] = $data['code'] ?? $this->generateCode();

                $promo = Promocode::create([
                    'code'       => $data['code'],
                    'type'       => intval($data['type']),
                    'start_date' => $data['start_date'] ?? null,
                    'end_date'   => $data['end_date'] ?? null,
                    'discount'   => intval($data['discount']),
                    'status'     => 0, // active
                ]);

                if (!empty($data['plans']) && is_array($data['plans'])) {
                    $promo->plans()->sync($data['plans']);
                }
                if (!empty($data['packages']) && is_array($data['packages'])) {
                    $promo->packages()->sync($data['packages']);
                }

                return $promo;
            } catch (QueryException $e) {
                if (!in_array($e->getCode(), ['23000', '23505'], true)) {
                    throw $e;
                }
                unset($data['code']);
            }
        }

        throw new \RuntimeException('Bir necha urinishda ham unique promo code yaratib bo‘lmadi.');
    }

    public function createMany(array $data, int $amount, int $tries = 5): array
    {
        $created = [];
        $sessionSeen = [];

        for ($i = 0; $i < $amount; $i++) {
            for ($g = 0; $g < $tries; $g++) {
                $candidate = $data['code'] ?? $this->generateCode();
                if (!isset($sessionSeen[$candidate])) {
                    $data['code'] = $candidate;
                    break;
                }
                unset($data['code']);
            }

            $promo = $this->create($data, $tries);
            $sessionSeen[$promo->code] = true;
            $created[] = $promo;
            unset($data['code']);
        }

        return $created;
    }
}
