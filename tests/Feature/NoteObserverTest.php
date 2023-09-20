<?php

namespace Tests\Feature;

use App\Jobs\CreateNoteForUserJob;
use App\Jobs\NoteCreatedJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NoteObserverTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function note_created_job_is_fired(): void {
        Queue::fake([
            NoteCreatedJob::class,
        ]);

        $user = User::factory()->create();

        // FAILING TESTS - Try to uncomment the ff, test will fail
        // 1. This is the error: If using DB transaction with createOrFirst, observer does not run.
        DB::transaction(fn() => $user->notes()->createOrFirst(['notes' => 'abc']));

        // 2. As mentioned by mpyw, double-wrap in DB::transaction() fails in tests
        //DB::transaction(fn() => DB::transaction(fn() => $user->notes()->createOrFirst(['notes' => 'abc'])));


        // PASSING TESTS - Try to uncomment the ff, test will pass
        // 1. Using createOrFirst() without DB::transaction
        //$user->notes()->createOrFirst(['notes' => 'abc']);

        // 2. Using create() with DB::transaction
        //DB::transaction(fn() => $user->notes()->create(['notes' => 'abc']));

        Queue::assertPushedOn("products", NoteCreatedJob::class);
    }
}
