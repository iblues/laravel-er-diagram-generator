<?php

namespace BeyondCode\ErdGenerator;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;

/**
 * generate json to vue
 * @author Blues
 * Class JsonGraphBuilder
 * @package BeyondCode\ErdGenerator
 */
class JsonGraphBuilder
{
    /** @var Graph */
    private $graph;

    /**
     * @param  Collection  $models
     * @return JsonGenerator
     */
    public function buildGraph(Collection $models): JsonGenerator
    {
        $this->graph = new JsonGenerator();

        $this->addModelsToGraph($models);

        return $this->graph;
    }

//    protected function getTableColumnsFromModel(EloquentModel $model)
//    {
//        try {
//            $table            = $model->getConnection()->getTablePrefix().$model->getTable();
//            $schema           = $model->getConnection()->getDoctrineSchemaManager($table);
//            $databasePlatform = $schema->getDatabasePlatform();
//            $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
//
//            $database = null;
//
//            if (strpos($table, '.')) {
//                list($database, $table) = explode('.', $table);
//            }
//
//            return $schema->listTableColumns($table, $database);
//        } catch (\Throwable $e) {
//        }
//
//        return [];
//    }
//
//    protected function getModelLabel(EloquentModel $model, string $label)
//    {
//        $table = '<<table width="100%" height="100%" border="0" margin="0" cellborder="1" cellspacing="0" cellpadding="10">'.PHP_EOL;
//        $table .= '<tr width="100%"><td width="100%" bgcolor="'.config('erd-generator.table.header_background_color').'"><font color="'.config('erd-generator.table.header_font_color').'">'.$label.'</font></td></tr>'.PHP_EOL;
//
//        if (config('erd-generator.use_db_schema')) {
//            $columns = $this->getTableColumnsFromModel($model);
//            foreach ($columns as $column) {
//                $label = $column->getName();
//                if (config('erd-generator.use_column_types')) {
//                    $label .= ' ('.$column->getType()->getName().')';
//                }
//                $table .= '<tr width="100%"><td port="'.$column->getName().'" align="left" width="100%"  bgcolor="'.config('erd-generator.table.row_background_color').'"><font color="'.config('erd-generator.table.row_font_color').'" >'.$label.'</font></td></tr>'.PHP_EOL;
//            }
//        }
//
//        $table .= '</table>>';
//
//        return $table;
//    }

    /**
     * step.1
     * @param  Collection  $models
     * @author Blues
     * @email i@iblues.name
     */
    protected function addModelsToGraph(Collection $models)
    {
        // Add models to graph
        $models->map(function (Model $model) {
            $eloquentModel = app($model->getModel());
            $this->addNodeToGraph($eloquentModel, $model->getNodeName(), $model->getLabel());
        });

        // Create relations
        $models->map(function ($model) {
            $this->addRelationToGraph($model);
        });
    }

    /**
     * step.2  add Model to generator
     * @param  EloquentModel  $eloquentModel
     * @param  string  $nodeName
     * @param  string  $relationName
     * @author Blues
     *
     */
    protected function addNodeToGraph(EloquentModel $eloquentModel, string $nodeName, string $relationName)
    {
        //create node object;
        $node = new JsonGeneratorNode($eloquentModel, $nodeName, $relationName);
        $this->graph->setNode($node);
    }

    /**
     * step.3  addRelationToGraph
     * @param  Model  $model
     * @author Blues
     *
     */
    protected function addRelationToGraph(Model $model)
    {
        // targetModel
        $modelNode = $this->graph->findNode($model->getNodeName());

        /** @var ModelRelation $relation */
        foreach ($model->getRelations() as $relation) {
            $relatedModelNode = $this->graph->findNode($relation->getModelNodeName());

            if ($relatedModelNode !== null) {
                $this->connectByRelation($model, $relation, $modelNode, $relatedModelNode);
            }
        }
    }

    /**
     * @param  JsonGeneratorNode  $modelNode
     * @param  JsonGeneratorNode  $relatedModelNode
     * @param  ModelRelation  $relation
     */
    protected function connectNodes(JsonGeneratorNode $modelNode, JsonGeneratorNode $relatedModelNode, ModelRelation $relation): void
    {

        $this->graph->link($modelNode,$relatedModelNode,$relation);
    }

    /**
     * @param  Model  $model
     * @param  ModelRelation  $relation
     * @param  JsonGeneratorNode  $modelNode
     * @param  JsonGeneratorNode  $relatedModelNode
     * @return void
     */
    protected function connectBelongsToMany(
        Model $model,
        ModelRelation $relation,
        JsonGeneratorNode $modelNode,
        JsonGeneratorNode $relatedModelNode
    ): void {
        $relationName  = $relation->getName();
        $eloquentModel = app($model->getModel());

        /** @var BelongsToMany $eloquentRelation */
        $eloquentRelation = $eloquentModel->$relationName();

        if (!$eloquentRelation instanceof BelongsToMany) {
            return;
        }

        $pivotClass = $eloquentRelation->getPivotClass();

        try {
            /** @var EloquentModel $relationModel */
            $pivotModel = app($pivotClass);
            $pivotModel->setTable($eloquentRelation->getTable());
            $label      = (new \ReflectionClass($pivotClass))->getShortName();
            $pivotTable = $eloquentRelation->getTable();
            $this->addNodeToGraph($pivotModel, $pivotTable, $label);

            $pivotModelNode = $this->graph->findNode($pivotTable);

            $relation = new ModelRelation(
                $relationName,
                'BelongsToMany',
                $model->getModel(),
                $eloquentRelation->getParent()->getKeyName(),
                $eloquentRelation->getForeignPivotKeyName()
            );

            $this->connectNodes($modelNode, $pivotModelNode, $relation);

            $relation = new ModelRelation(
                $relationName,
                'BelongsToMany',
                $model->getModel(),
                $eloquentRelation->getRelatedPivotKeyName(),
                $eloquentRelation->getRelated()->getKeyName()
            );

            $this->connectNodes($pivotModelNode, $relatedModelNode, $relation);
        } catch (\ReflectionException $e) {
        }
    }

    /**
     * step.4  connectModels
     * @param  Model  $model
     * @param  ModelRelation  $relation
     * @param  JsonGeneratorNode  $modelNode
     * @param  JsonGeneratorNode  $relatedModelNode
     */
    protected function connectByRelation(
        Model $model,
        ModelRelation $relation,
        JsonGeneratorNode $modelNode,
        JsonGeneratorNode $relatedModelNode
    ): void {
        if ($relation->getType() === 'BelongsToMany') {
            $this->connectBelongsToMany($model, $relation, $modelNode, $relatedModelNode);
            return;
        }

        $this->connectNodes($modelNode, $relatedModelNode, $relation);
    }
}
