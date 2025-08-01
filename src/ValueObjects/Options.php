<?php

namespace Prelude\SDK\ValueObjects;

class Options
{
    public function __construct(
        private string $_templateId,
        private array $_variables = []
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'template_id' => $this->_templateId,
            'variables' => $this->_variables,
        ];
    }
}
