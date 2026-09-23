<?php

namespace Tests\Unit;

use App\Models\Graffiti;
use App\Models\Tag;
use PHPUnit\Framework\TestCase;

class TagTextTest extends TestCase
{
    public function test_normalize_ignores_case_spaces_and_accents(): void
    {
        $this->assertSame('KASE1', Tag::normalize('kasé 1'));
        $this->assertSame('KASE1', Tag::normalize(' K-A-S-E_1 '));
        $this->assertSame('', Tag::normalize('!!!'));
    }

    public function test_distance_in_meters(): void
    {
        // 0.0001 grados de latitud son unos 11 metros.
        $d = Graffiti::metersBetween(-33.4489, -70.6693, -33.4490, -70.6693);

        $this->assertEqualsWithDelta(11.1, $d, 0.2);
    }
}
