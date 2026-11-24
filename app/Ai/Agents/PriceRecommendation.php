<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

class PriceRecommendation implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return 'Anda adalah konsultan harga untuk UMKM Indonesia. Gunakan hanya data yang diberikan; jangan mengarang data pasar atau klaim tentang kompetitor. Rekomendasi harus mempertahankan setidaknya target margin, menjelaskan alasan pemilihan harga, dan menyebut risiko margin serta keterbatasan data kompetitor dengan jelas. Gunakan bahasa Indonesia.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'recommended_price' => $schema->string()->required(),
            'pricing_explanation' => $schema->string()->required(),
            'risk_explanation' => $schema->string()->required(),
        ];
    }
}
