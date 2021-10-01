<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Components\SessionHolder\SessionHolderManager;
use App\Traits\TransformIt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use League\Fractal\TransformerAbstract;

class AppServiceProvider extends ServiceProvider
{
    use TransformIt;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('session-holder', function ($app) {
            return new SessionHolderManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupRouteMacros();
        $this->setupTransformationMacros();

        // For debugging purpose
        Builder::macro('ddd', function () {
            $sql = str_replace(['%', '?'], ['%%', '%s'], $this->toSql());

            $handledBindings = array_map(function ($binding) {
                if (is_numeric($binding)) {
                    return $binding;
                }

                $value = str_replace(['\\', "'"], ['\\\\', "\'"], $binding);

                return "'{$value}'";
            }, $this->getConnection()->prepareBindings($this->getBindings()));

            $fullSql = vsprintf($sql, $handledBindings);

            dd($fullSql);
        });

        Validator::extend('without_spaces', function ($attr, $value) {
            return preg_match('/^\S*$/u', $value);
        }, 'The :attribute field could not have a space.');

        Validator::extend('file_exists_and_readable', function ($attr, $value) {
            /** @var UploadedFile $value */
            return file_exists($value->path()) && $value->isReadable();
        }, 'The :attribute file does not exist or is not readable.');
        Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');
    }

    protected function setupRouteMacros()
    {
        Route::macro('softDeleteRoutes', function ($name, $controller) {
            Route::prefix($name)->group(function () use ($controller) {
                Route::get('/trashed', $controller . '@trashed');
                Route::put('/restore/{id}', $controller . '@restore');
                Route::delete('/permanently-delete/{id}', $controller . '@permanentlyDelete');
                Route::delete('/permanently-delete/{bulk}', $controller . '@permanentlyDelete');
            });
        });
    }

    protected function setupTransformationMacros()
    {
        $dummyThis = $this;

        Collection::macro('transformIt', function (TransformerAbstract $transformer = null) use ($dummyThis) {
            return $dummyThis->baseTransform($this, $transformer);
        });
        Builder::macro('transformIt', function (TransformerAbstract $transformer = null) use ($dummyThis) {
            return $dummyThis->baseTransform($this, $transformer);
        });
    }
}
