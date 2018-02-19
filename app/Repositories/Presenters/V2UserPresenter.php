<?php

namespace App\Repositories\Presenters;

use App\Repositories\Transformers\V2UserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class V2UserPresenter
 *
 * @package namespace App\Repositories\Presenters;
 */
class V2UserPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new V2UserTransformer();
    }
}
