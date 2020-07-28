<?php


namespace BeyondCode\ErdGenerator;


class JsonGeneratorNode
{

    protected $model;
    protected $nodeName;
    protected $relationName;

    public function __construct($model = null, $nodeName = null, $relationName = null)
    {
        $this->nodeName     = $nodeName;
        $this->model        = $model;
        $this->relationName = $relationName;
    }

    public function getName()
    {
        return $this->nodeName;
    }


}
