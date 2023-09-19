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
        DB::transaction(fn() => $user->notes()->createOrFirst(['notes' => 'abc']));

        Queue::assertPushedOn("products", NoteCreatedJob::class);
    }
}
