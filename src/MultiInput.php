<?php

namespace Jshxl\MultiInput;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class MultiInput extends Field
{
    use SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'multi-input';

    /**
     * 请求链接
     *
     * @var string|array
     * */
    public string|array $options;

    /**
     * table显示的字段
     *
     * @var array
     * */
    public array $columns = [];

    /**
     * table是否显示索引
     *
     * @var bool
     * */
    public bool $withIndex = false;

    /**
     * 将字段值转换为整数
     *
     * @var bool
     * */
    public bool $formatInt = false;

    /**
     * 是否支持录入字母
     *
     * @var bool
     * */
    public bool $supportABC = false;

    /**
     * 请求链接
     * @param string|array $options
     *
     * @return self
     * */
    public function options(string|array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * table显示的字段
     * @param array $columns
     *
     * @return self
     * */
    public function columns(array $columns = []): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * table是否显示索引
     * @param bool $boolean
     *
     * @return self
     * */
    public function withIndex(bool $boolean = true): self
    {
        $this->withIndex = $boolean;
        return $this;
    }

    /**
     * 将字段值转换为整数
     * @param bool $boolean
     *
     * @return self
     */
    public function formatInt(bool $boolean = true): self
    {
        $this->formatInt = $boolean;
        return $this;
    }

    /**
     * 是否支持录入字母
     * @param bool $boolean
     *
     * @return self
     * */
    public function supportABC(bool $boolean = true): self
    {
        $this->supportABC = $boolean;
        return $this;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param Model $model
     * @param string $attribute
     *
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute): void
    {
        if (!$request->exists($requestAttribute)) return;

        $value = collect(json_decode($request->input($requestAttribute), true))
            ->map(function ($item) {
                return $this->formatInt ? intval($item) : $item;
            })
            ->all();
        $model->forceFill([Str::replace('.', '->', $attribute) => $value]);
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function jsonSerialize(): array
    {
        if (!isset($this->options)) {
            throw new \Exception(__('MultiInput\' option is required'));
        }

        return with(app(NovaRequest::class), function ($request) {
            return array_merge([
                'options'    => $this->options ?? [],
                'columns'    => $this->columns,
                'withIndex'  => $this->withIndex,
                'formatInt'  => $this->formatInt,
                'supportABC' => $this->supportABC,
            ], parent::jsonSerialize());
        });
    }
}
