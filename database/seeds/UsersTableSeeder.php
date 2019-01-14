<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $_COOKIE['id'] = 0;

        factory(App\User::class, 10)
            ->create()
            ->each(
                function ($u) {
                    $_COOKIE['id'] = $u->id;

                    $u->building()->save(
                        factory(App\Models\Building::class)->make()
                    );

                    $u->resource()->save(
                        factory(App\Models\Resource::class)->make()
                    );

                    foreach (range(1, 3) as $value) {
                        $u->buildingList()->save(
                            factory(App\Models\BuildingList::class)->make()
                        );
                    }
                }
            );
    }
}
