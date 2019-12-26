<?php

namespace Tests\Unit;

use App\User;
use App\Repositories\UserRepository;
use App\Services\FooService;
use Tests\TestCase;

class FooServiceTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
 
        $this->service = app(FooService::class);
        $this->repository = mock(UserRepository::class);
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
        $topScorer = $this->service->etTopScorerFromArray($this->scorerArray);

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
            'first_name' => $faker->name,
            'last_name' => $faker->name,
            'email' => $faker->email,
            'address' => $faker->address
        ]);

        $input = [
            'address' => $faker->address
        ];

        $user = $this->service->update($input);

        $this->assertEquals($user->address, $input['address']);
    }

    /**
     * Return only the fashionphile users based on their emails.
     *
     * @return void
     */
    public function testFetchFashionphileEmails()
    {
        $users = factory(App\User::class, 3)->make([
            'first_name' => $faker->name,
            'last_name' => $faker->name,
            'email' => sprintf("%s@gmail.com", $faker->name),
            'address' => $faker->address
        ]);

        $users->merge(factory(App\User::class, 4)->make([
            'first_name' => $faker->name,
            'last_name' => $faker->name,
            'email' => sprintf("%s@fashionphile.com", $faker->name),
            'address' => $faker->address
        ]));

        $users->merge(factory(App\User::class, 2)->make([
            'first_name' => $faker->name,
            'last_name' => $faker->name,
            'email' => sprintf("%s@ymail.com", $faker->name),
            'address' => $faker->address
        ]));

        $this->userRepository->shouldReceive('fetchUsers')->andReturn($users);

        $fetchedUser = $this->service->fetchFashionphileUsers();

        $this->assertEquals(count($users), 4);
        $this->assertEquals($users->count(), 4);
    }
}
