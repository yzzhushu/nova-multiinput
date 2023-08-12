<?php

namespace Jshxl\MultiInput;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class MultiInput extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'multi-input';

    /**
     * 请求链接
     *
     * @var string|null
     * */
    public string|null $options;

    /**
     * 请求链接
     * @param string|null $options
     *
     * @return self
     * */
    public function options(string|null $options): self
    {
        $this->options = $options;
        return $this;
    }
}
