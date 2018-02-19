<?php

namespace App\Repositories\Transformers\Traits;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

trait Morpher
{
    /**
     * Morph Object Item Data
     *
     * @param Object $object
     * @param Object $objectModel
     * @param Object $objectTransformer
     * @return void
     */
    public function morphItem($object, $objectModel, $objectTransformer)
    {
        if (empty($object))
            $object = $objectModel;

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $resource = $this->item($object, $objectTransformer);
        $collection = $manager->createData($resource)->toArray();

        return $collection['data'];
    }

    /**
     * Morph Object Colelction Data
     *
     * @param Object $object
     * @param Object $objectModel
     * @param Object $objectTransformer
     * @return void
     */
    public function morphCollection($object, $objectModel, $objectTransformer)
    {
        if (empty($object))
            $object = $objectModel;

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $resource = $this->collection($object, $objectTransformer);
        $collection = $manager->createData($resource)->toArray();

        return $collection['data'];
    }

}