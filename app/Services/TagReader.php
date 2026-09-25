<?php

namespace App\Services;

use Anthropic\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Le pide a Claude (la IA de Anthropic) que lea qué dice el tag de una foto.
 * Es solo una sugerencia: la persona siempre lo revisa antes de registrar.
 */
class TagReader
{
    private const PROMPT = <<<'TXT'
        La foto muestra un grafiti en la calle. Lee el tag (la firma o nombre del grafitero) que se ve en la foto.
        Los tags suelen tener letras muy estilizadas; léelas como lo haría alguien que conoce el grafiti.
        Si hay varios, elige el más grande o el que está más al centro.
        Responde solo con el texto del tag en mayúsculas, sin comillas ni explicaciones.
        Si no se ve ningún tag o no se puede leer, responde exactamente: NADA
        TXT;

    public function enabled(): bool
    {
        return filled(config('kingtag.reader.api_key'));
    }

    /**
     * El texto del tag, o null si la IA no lo pudo leer (o no está activada).
     */
    public function read(UploadedFile $photo): ?string
    {
        if (! $this->enabled()) {
            return null;
        }

        try {
            $client = new Client(apiKey: config('kingtag.reader.api_key'));

            $message = $client->messages->create(
                model: config('kingtag.reader.model'),
                maxTokens: 50,
                messages: [[
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'mediaType' => $photo->getMimeType(),
                                'data' => base64_encode($photo->getContent()),
                            ],
                        ],
                        ['type' => 'text', 'text' => self::PROMPT],
                    ],
                ]],
            );
        } catch (Throwable $e) {
            Log::warning('No se pudo leer el tag con IA: '.$e->getMessage());

            return null;
        }

        $text = '';
        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                $text .= $block->text;
            }
        }

        return $this->clean($text);
    }

    private function clean(string $text): ?string
    {
        $text = mb_strtoupper(trim($text, " \t\n\r\"'.«»“”"));

        if ($text === '' || $text === 'NADA' || mb_strlen($text) > 60 || str_contains($text, "\n")) {
            return null;
        }

        return $text;
    }
}
