<?php
class Fieldtype_textarea extends Fieldtype
{
    public function render()
    {
        $height = isset($this->field_config['height']) ? $this->field_config['height'] . 'px' : '150px';
        $required_str = ($this->is_required) ? "data-required='true'" : '';
        $html   = "<textarea name='{$this->fieldname}' tabindex='{$this->tabindex}' style='height: {$height}' {$required_str}>";
        $html  .= HTML::convertEntities($this->field_data) . "</textarea>";

        if (array_get($this->field_config, 'code_formatting', false)) {
            $html = "<pre><code>" . $html . "</code></pre>";
        }

        return $html;
    }
}
