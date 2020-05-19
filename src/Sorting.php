<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class Sort
 * @package App\Domain\Core
 */
class Sorting
{
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';
    public const SORT_DIRS = [
        self::SORT_ASC,
        self::SORT_DESC,
    ];

    public const DEFAULT_SORT_FIELD = 'createdAt';

    public const DESC_SORT_SYMBOL = '-';

    /**
     * @var array
     */
    private array $sort;

    /**
     * Sorting constructor.
     * @param array $sort
     * @param array $availableFields
     * @throws ValidationException
     */
    public function __construct(array $sort, array $availableFields)
    {
        $this->validate($sort, $availableFields);
        $this->sort = $sort;
    }

    /**
     * @param Request $request
     * @param array $availableFields
     * @return Sorting
     * @throws ValidationException
     */
    public static function fromRequest(Request $request, array $availableFields): Sorting
    {
        $sort = $request->get('sort');

        if (!$sort) {
            return new Sorting(
                [
                    [
                        'field' => self::DEFAULT_SORT_FIELD,
                        'dir' => self::SORT_DESC
                    ]
                ],
                $availableFields
            );
        }

        $fields = collect(explode(',', $sort))
            ->map(
                function (string $field) {
                    if (mb_substr($field, 0, 1) == self::DESC_SORT_SYMBOL) {
                        return [
                            'field' => mb_substr($field, 1),
                            'dir' => self::SORT_DESC
                        ];
                    }
                    return [
                        'field' => $field,
                        'dir' => self::SORT_ASC
                    ];
                }
            )
            ->toArray();

        return new Sorting($fields, $availableFields);
    }

    /**
     * @return array
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    /**
     * @param array $fields
     * @param array $availableFields
     * @throws ValidationException
     */
    private function validate(array $fields, array $availableFields): void
    {
        $validator = $this->getValidationFactory()->make(
            [
                'fields' => $fields
            ],
            [
                'fields.*.field' => 'string|in:' . $this->snakeToCamelCase(implode(',', $availableFields))
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Get a validation factory instance.
     *
     * @return Factory
     */
    protected function getValidationFactory()
    {
        return app('validator');
    }

    /**
     * @param $key
     * @return string
     */
    private function snakeToCamelCase($key): string
    {
        return lcfirst(str_replace('_', "", ucwords($key, "/_")));
    }
}
