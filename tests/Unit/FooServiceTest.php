<?php

namespace Tests\Unit;

use App\User;
use App\Repositories\UserRepository;
use App\Services\FooService;
use Faker\Generator as Faker;
use Mockery;
use Tests\TestCase;

class FooServiceTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
 
        $this->repository = $this->mock(UserRepository::class);

        $this->faker = app(Faker::class);
        $this->service = app(FooService::class);
        $this->scorerArray = [
            'jazz' => [
                'malone' => 28.2,
                'stockton' => 14,
                'hornacek' => 8,
            ],
            'lakers' => [
                'johnson' => 23.5,
            ],
            'bulls' => [
                'jordan' => 32.8,
                'pippen' => 13.1,
            ]
        ];
    }

    /**
     * Convert string to int.
     *
     * @return void
     */
    public function testConvertStringToInt()
    {
        $response = $this->service->convertStringToInt("12");

        $this->assertTrue(is_int($response));
    }

    /**
     * Parse through the array to return the name of the highest scoring player.
     *
     * @return void
     */
    public function testParseForMostPoints()
    {
        $topScorer = $this->service->setTopScorerFromArray($this->scorerArray);

        $this->assertEquals($topScorer, 'jordan');
    }

    /**
     * Return the average of the scorers.
     *
     * @return void
     */
    public function testGetAverageOfScorers()
    {
        $avgScore = $this->service->avgScores($this->scorerArray);

        $this->assertEquals($avgScore, 19.93);
    }

    /**
     * Test updating the address for the user.
     *
     * @return void
     */
    public function testUpdateAddressForUser()
    {
        $user = factory(User::class)->make([
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'email' => $this->faker->email,
            'address' => $this->faker->address
        ]);

        $input = [
            'address' => $this->faker->address
        ];

        $user = $this->service->update($user, $input);

        $this->assertEquals($user->address, $input['address']);
    }

    /**
     * Return only the fashionphile users based on their emails.
     *
     * @return void
     */
    public function testFetchFashionphileEmails()
    {
        $users = factory(User::class, 3)->make([
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'email' => sprintf("%s@gmail.com", $this->faker->name),
            'address' => $this->faker->address
        ]);

        $users = $users->toBase()->merge(factory(User::class, 4)->make([
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'email' => sprintf("%s@fashionphile.com", $this->faker->name),
            'address' => $this->faker->address
        ]));

        $users = $users->toBase()->merge(factory(User::class, 2)->make([
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'email' => sprintf("%s@ymail.com", $this->faker->name),
            'address' => $this->faker->address
        ]));

        $this->repository->shouldReceive('fetchUsers')->once()->andReturn($users);

        $fetchedUser = $this->service->fetchFashionphileUsers();

        $this->assertEquals(count($fetchedUser), 4);
        $this->assertEquals($fetchedUser->count(), 4);
    }
}
