<?php

require_once __DIR__.'/vendor/autoload.php';

use OneToMany\AI\AI;
use OneToMany\AI\Bridge\Gemini\FileProvider as GeminiFileProvider;
use OneToMany\AI\Bridge\Gemini\Normalizer\QueryNormalizer as GeminiQueryNormalizer;
use OneToMany\AI\Bridge\Gemini\QueryProvider;
use OneToMany\AI\Bridge\OpenAI\Normalizer\QueryNormalizer as OpenAIQueryNormalizer;
use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Model;
use OneToMany\AI\Resource\File\RemoteFile;
use OneToMany\AI\Resource\Files;
use OneToMany\AI\Resource\Queries;
use OneToMany\AI\Resource\Query\InputText;
use OneToMany\AI\Resource\Query\Prompt;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\Serializer;

// Create the Symfony Serializer component
$typeExtractor = new PropertyInfoExtractor([], [
    new ConstructorExtractor([
        new PhpDocExtractor(),
        new PhpStanExtractor(),
        new ReflectionExtractor(),
    ]),
]);

$normalizers = [
    new BackedEnumNormalizer(),
    new DateTimeNormalizer(),
    new ArrayDenormalizer(),
    new UnwrappingDenormalizer(),
    new GeminiQueryNormalizer(),
    new OpenAIQueryNormalizer(),
    new ObjectNormalizer(
        null,
        null,
        null,
        $typeExtractor,
    ),
];

$serializer = new Serializer($normalizers, [
    new JsonEncoder(),
    new XmlEncoder(),
]);

// Create the Symfony HTTP Client
$httpClient = HttpClient::create([
    'timeout' => 60.0,
]);

$transport = new Transport($httpClient, $serializer);

$files = new Files([
    new GeminiFileProvider($transport, $serializer, getenv('GEMINI_API_KEY')),
]);

$queries = new Queries([
    new QueryProvider($transport, $serializer, getenv('GEMINI_API_KEY')),
]);

$ai = new AI($files, $queries);

$prompt = Prompt::with(new InputText('Briefly describe this image.'), new RemoteFile('gemini', 'files/bzyhaiqkq978', 'image/png', 'https://generativelanguage.googleapis.com/v1beta/files/bzyhaiqkq978'));

try {
    $query = $ai->queries->compile(Model::create('gemini:gemini-3.7-flash'), $prompt);

    $response = $ai->queries->run($query);
    print_r($response);
} catch (ExceptionInterface $e) {
    printf("%s\n", $e->getMessage());
}


// $file = $ai->files->upload('gemini', '/Users/vic/Downloads/fs-app1.png');
// print_r($file);
