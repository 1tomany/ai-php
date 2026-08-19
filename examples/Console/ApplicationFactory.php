<?php

namespace OneToMany\AI\Example\Console;

use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Example\Console\Command\DeleteFileCommand;
use OneToMany\AI\Example\Console\Command\RunQueryCommand;
use OneToMany\AI\Example\Console\Command\UploadFileCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function is_file;

final readonly class ApplicationFactory
{
    public static function create(
        string $projectDirectory,
        ?HttpClientInterface $httpClient = null,
    ): Application {
        self::loadEnvironment($projectDirectory);

        $typeExtractor = new PropertyInfoExtractor([], [
            new ConstructorExtractor([
                new PhpDocExtractor(),
                new PhpStanExtractor(),
                new ReflectionExtractor(),
            ]),
        ]);

        $serializer = new Serializer([
            new BackedEnumNormalizer(),
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            new UnwrappingDenormalizer(),
            new ObjectNormalizer(
                null,
                null,
                null,
                $typeExtractor,
            ),
        ], [
            new JsonEncoder(),
        ]);

        $factory = new AIFactory(new Transport(
            $httpClient ?? HttpClient::create(['timeout' => 120.0]),
            $serializer,
        ));
        $apiKeys = new ApiKeyResolver();

        $application = new Application('OneToMany AI');
        $application->addCommands([
            new UploadFileCommand($factory, $apiKeys),
            new DeleteFileCommand($factory, $apiKeys),
            new RunQueryCommand($factory, $apiKeys),
        ]);

        return $application;
    }

    private static function loadEnvironment(string $projectDirectory): void
    {
        $path = $projectDirectory.'/.env';

        if (is_file($path)) {
            new Dotenv()->loadEnv($path);
        }
    }
}
