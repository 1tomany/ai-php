<?php

require_once __DIR__.'/vendor/autoload.php';

$queryNormalizer = new OneToMany\AI\Bridge\Gemini\Normalizer\QueryNormalizer();
$model = new OneToMany\AI\Model(OneToMany\AI\Provider::OpenAI, 'gpt-5.6-luna');

$prompt = new OneToMany\AI\Resource\Query\Prompt()
    ->addInputText('Describe this image for me.')
    ->addRemoteFile(new OneToMany\AI\Resource\File\RemoteFile('openai', 'abc_123', 'image/jpeg'));

$output = $queryNormalizer->normalize(new OneToMany\AI\Resource\Query\QueryDefinition($model, $prompt));

var_dump(json_encode($output));
