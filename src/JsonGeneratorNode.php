<?php


namespace BeyondCode\ErdGenerator;


class JsonGeneratorNode
{

    protected $model;
    protected $nodeName;
    protected $shortModelName;
    protected $relations = [];

    public function __construct($model = null, $nodeName = null, $shortModelName = null)
    {
        //unique key
        $this->nodeName       = $nodeName;
        $this->model          = $model;
        $this->shortModelName = $shortModelName;
    }

    public function getName()
    {
        //todo 解析注释
        //todo 读取数据库结构
        return $this->nodeName;
    }


    /**
     * @param  JsonGeneratorNode  $relationNode
     * @param  ModelRelation  $relation
     * @author Blues
     */
    public function setRelation(JsonGeneratorNode $relationNode, ModelRelation $relation)
    {
        $name = $relation->getName();//relationName like users
        $this->relations[$name] = [
            'nodeName' => $relationNode->nodeName,
            'localKey' => $relation->getLocalKey(),
            'name' => $name,
            'foreignKey' => $relation->getForeignKey(),
            'model' => $relation->getModel(),
            'type'=>$relation->getType()
        ];
    }

    public function getRelations()
    {
        return $this->relations;
    }


}
