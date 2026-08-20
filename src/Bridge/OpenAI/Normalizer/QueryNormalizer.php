<?php

namespace OneToMany\AI\Bridge\OpenAI\Normalizer;

use OneToMany\AI\Bridge\OpenAI\Resource\Response\EasyInputMessage;
use OneToMany\AI\Bridge\OpenAI\Resource\Response\ResponseInput;
use OneToMany\AI\Resource\Query\InputText;
use OneToMany\AI\Resource\Query\QueryDefinition;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_replace;

final readonly class QueryNormalizer implements NormalizerInterface
{
    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     *
     * @param QueryDefinition $data
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $easyInputMessage = new EasyInputMessage();

        foreach ($data->getPrompt()->getInput() as $input) {
            if ($input instanceof InputText) {
                $content = ResponseInput::asText(
                    text: $input->getText(),
                );
            } else {
                $content = ResponseInput::asFile($input->getId(), $input->getMimeType());
            }

            $easyInputMessage->addContent($content);
        }

        $request = [
            'model' => $data->getModel()->getName(),
            'input' => [
                $easyInputMessage,
            ],
        ];

        if ($instructions = $data->getPrompt()->getInstructions()) {
            $request['instructions'] = $instructions->getText();
        }

        if ($schema = $data->getPrompt()->getSchema()) {
            $request['text'] = [
                'format' => [
                    'type' => 'json_schema',
                    'name' => $schema->getName(),
                    'strict' => $schema->isStrict(),
                    'schema' => $schema->getSchema(),
                ],
            ];
        }

        return array_replace($data->getOptions(), $request);
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof QueryDefinition && $data->getModel()->getVendor()->isOpenAI();
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            QueryDefinition::class => false,
        ];
    }
}
