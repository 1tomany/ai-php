# AI and LLM Library for PHP

This library provides a single, unified, framework-independent library for integration with several popular AI platforms and large language models.

## Installation

Install the library using Composer:

```shell
composer require 1tomany/ai-php
```

### Symfony Bundle

A [Symfony bundle](https://github.com/1tomany/ai-php-bundle) is available if you wish to integrate this library into your Symfony applications with autowiring and configuration support.

## Supported platforms

- Gemini
- OpenAI

### Platform feature support

**Note:** Each platform refers to generating output (inference) differently; OpenAI uses "Response", Gemini uses "Interaction", and Anthropic uses "Message". I've decided the word "Query" best represents how you interact with a generative LLM: you compile a query and then run the query to generate a response. The word "Embedding" will continue to be used for embedding models as that is more or less standard across different providers.

To generate a response, you must first compile a query. A query is made up of different input components: text prompts, files, a JSON schema, and/or system instructions. Once the query is compiled, it can be sent to the LLM for analysis.

This library allows you to compile a query before sending it to the model for two reasons:

1. You can log/analyze the request payload before sending it.
2. You can compile individual requests for batching.

| Feature     | Gemini | OpenAI |
| ----------- | :----: | :----: |
| **Files**   |        |        |
| Upload      |   ✅   |   ✅   |
| Delete      |   ✅   |   ✅   |
| **Queries** |        |        |
| Compile     |   ✅   |   ✅   |
| Run         |   ✅   |   ✅   |

## Credits

- [Vic Cherubini](https://github.com/viccherubini), [1:N Labs, LLC](https://1tomany.com)

## License

The MIT License
