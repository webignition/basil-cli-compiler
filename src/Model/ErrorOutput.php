<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Model;

class ErrorOutput extends AbstractOutput implements ErrorOutputInterface
{
    public const CODE_UNKNOWN = 99;
    public const CODE_COMMAND_CONFIG_SOURCE_EMPTY = 100;
    public const CODE_COMMAND_CONFIG_SOURCE_INVALID_DOES_NOT_EXIST = 101;
    public const CODE_COMMAND_CONFIG_SOURCE_INVALID_NOT_READABLE = 102;
    public const CODE_COMMAND_CONFIG_TARGET_EMPTY = 103;
    public const CODE_COMMAND_CONFIG_TARGET_INVALID_DOES_NOT_EXIST = 104;
    public const CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_A_DIRECTORY = 105;
    public const CODE_COMMAND_CONFIG_TARGET_INVALID_NOT_WRITABLE = 106;
    public const CODE_LOADER_INVALID_YAML = 200;
    public const CODE_LOADER_CIRCULAR_STEP_IMPORT = 201;
    public const CODE_LOADER_EMPTY_TEST = 202;
    public const CODE_LOADER_INVALID_PAGE = 203;
    public const CODE_LOADER_INVALID_TEST = 204;
    public const CODE_LOADER_NON_RETRIEVABLE_IMPORT = 205;
    public const CODE_LOADER_UNPARSEABLE_DATA = 206;
    public const CODE_LOADER_UNKNOWN_ELEMENT = 207;
    public const CODE_LOADER_UNKNOWN_ITEM = 208;
    public const CODE_LOADER_UNKNOWN_PAGE_ELEMENT = 209;
    public const CODE_LOADER_UNKNOWN_TEST = 210;
    public const CODE_GENERATOR_UNRESOLVED_PLACEHOLDER = 211;
    public const CODE_GENERATOR_UNSUPPORTED_STEP = 212;

    private int $code;
    private string $message;

    /**
     * @var array<mixed>
     */
    private array $context;

    /**
     * @param Configuration $configuration
     * @param string $message
     * @param int $code
     * @param array<mixed> $context
     */
    public function __construct(
        Configuration $configuration,
        string $message,
        int $code,
        array $context = []
    ) {
        parent::__construct($configuration);

        $this->code = $code;
        $this->message = $message;
        $this->context = $context;
    }


    public function getCode(): int
    {
        return $this->code;
    }

    public function getData(): array
    {
        $errorData = [
            'message' => $this->message,
            'code' => $this->getCode(),
        ];

        if ([] !== $this->context) {
            $errorData['context'] = $this->context;
        }

        $serializedData = parent::getData();
        $serializedData['error'] = $errorData;

        return $serializedData;
    }

    /**
     * @param array<mixed> $data
     *
     * @return ErrorOutput
     */
    public static function fromArray(array $data): ErrorOutput
    {
        $configData = $data['config'] ?? [];
        $errorData = $data['error'] ?? [];
        $contextData = $errorData['context'] ?? [];

        return new ErrorOutput(
            Configuration::fromArray($configData),
            $errorData['message'] ?? '',
            (int) ($errorData['code'] ?? self::CODE_UNKNOWN),
            $contextData
        );
    }
}
