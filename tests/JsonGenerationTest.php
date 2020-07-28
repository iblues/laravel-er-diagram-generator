<?php

namespace BeyondCode\ErdGenerator\Tests;

use Spatie\Snapshots\MatchesSnapshots;
use Illuminate\Support\Facades\Artisan;

class JsonGenerationTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function it_generated_graphviz_for_test_models()
    {
        $this->app['config']->set('erd-generator.use_db_schema', false);
        $this->app['config']->set('erd-generator.directories', [__DIR__.'/Models']);

        Artisan::call('generate:erd', [
            'test.json',
            '--format' => 'json'
        ]);

        $this->assertMatchesSnapshot(Artisan::output());
    }

//    /** @test */
//    public function it_generated_graphviz_for_test_models_with_db_columns_and_types()
//    {
//        $this->app['config']->set('erd-generator.directories', [__DIR__.'/Models']);
//
//        Artisan::call('generate:erd', [
//            '--format' => 'json'
//        ]);
//
//        $this->assertMatchesSnapshot(Artisan::output());
//    }


}
