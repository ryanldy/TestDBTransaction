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
    public function user_created_job_is_fired(): void {
        Queue::fake([
            NoteCreatedJob::class,
        ]);

        $user = User::factory()->create();

        // This is the error: If using DB transaction with createOrFirst, observer does not run.
        DB::transaction(fn() => $user->notes()->createOrFirst(['notes' => 'abc']));

        // Try to uncomment this one and comment the line above, test will now pass.
        //$user->notes()->createOrFirst(['notes' => 'abc']);

        Queue::assertPushedOn("products", NoteCreatedJob::class);
    }
}
