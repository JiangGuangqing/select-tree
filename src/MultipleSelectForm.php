<?php

namespace Zhpefe\SelectTree;

use Encore\Admin\Form\Field\MultipleSelect;
use Illuminate\Support\Arr;

class MultipleSelectForm extends MultipleSelect
{
    protected $view = 'select-tree::multipleselect';

    protected $url = null;
    protected $top_id = 0;

    public function ajax($url, $idField = 'id', $textField = 'text')
    {
        $this->url = $url;
        return $this;
    }

    public function topID($id)
    {
        $this->top_id = $id;
        return $this;
    }

    public function render()
    {
        $vars = [
            'top_id' => $this->top_id,
            'url' => $this->url,
        ];
        if( ! $this->url ){
            Handler::error('Error', 'select-tree: You need $form->multiple_select_tree(column,label)->ajax()');
        }
        return parent::render()->with(compact('vars'));
    }

    /**
     * {@inheritdoc}
     */
    public function fill($data)
    {
        $relations = Arr::get($data, $this->column);

        if (is_string($relations)) {
            $this->value = explode(',', $relations);
        }

        if (!is_array($relations)) {
            return;
        }

        $first = current($relations);

        if (is_null($first)) {
            $this->value = null;

            // MultipleSelect value store as an ont-to-many relationship.
        } elseif (is_array($first)) {
            foreach ($relations as $key =>$value) {
                $tagid = Arr::get($value, "id");
                $tagname = Arr::get($value, "tagName");
                $this->value[$key]['tagid']=$tagid;
                $this->value[$key]['tagname']=$tagname;
            }
            $str='';
            foreach ($this->value as $value){
                $value = join(",",$value);
                $temp[] = $value;
            }
            foreach($temp as $v){
                $str.=$v."|";
            }
            $str = substr($str,0,-1);
//            var_dump('<pre>');var_dump($str);exit;
            $this->value=$str;
            // MultipleSelect value store as a column.
        } else {
            $this->value = '';
        }
    }
}